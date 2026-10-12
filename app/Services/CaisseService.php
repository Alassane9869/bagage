<?php

namespace App\Services;

use App\Models\Bagage;
use App\Models\Caisse;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CaisseService
{
    /**
     * Met à jour ou crée l'entrée de caisse journalière pour un agent.
     */
    public function updateCaisseJournaliere(User $user, float $montant, float $remise): void
    {
        $today = now()->toDateString();

        $caisse = Caisse::where('user_id', $user->id)
            ->whereDate('date_caisse', $today)
            ->first();

        if (!$caisse) {
            $caisse = Caisse::create([
                'user_id'       => $user->id,
                'date_caisse'   => $today,
                'total_bagages' => 0,
                'recettes'      => 0,
                'remises'       => 0,
                'net'           => 0,
            ]);
        }

        $caisse->increment('total_bagages');
        $caisse->increment('recettes', $montant);
        $caisse->increment('remises', $remise);
        $caisse->increment('net', $montant - $remise);
    }

    /**
     * Stats journalières d'un agent (Ma Caisse).
     */
    public function getMaCaisse(User $user, string $date = null): array
    {
        $date ??= now()->toDateString();

        $bagages = Bagage::where('user_id', $user->id)
            ->whereDate('date_enregistrement', $date)
            ->with(['voyage', 'client'])
            ->orderBy('date_enregistrement', 'desc')
            ->get();

        return [
            'nb_bagages'   => $bagages->count(),
            'recettes'     => $bagages->sum('montant'),
            'remises'      => $bagages->sum('remise'),
            'net'          => $bagages->sum(fn($b) => $b->montant - $b->remise),
            'transactions' => $bagages,
            'date'         => $date,
        ];
    }

    /**
     * Caisse ventilée par programme de départ pour un agent.
     *
     * BUG CORRIGÉ : le GROUP BY utilisait COALESCE() dans une expression MySQL
     * ce qui générait une erreur "not in GROUP BY". On groupe maintenant sur les
     * colonnes sources avant de les formater.
     */
    public function getCaisseParDepart(User $user, string $date = null): array
    {
        $date ??= now()->toDateString();

        // Bagages avec voyage lié — on groupe sur programme + ville pour éviter
        // l'erreur MySQL 1055 "not in GROUP BY"
        $concatProgramme = DB::getDriverName() === 'sqlite'
            ? "(voyages.ville_depart || '-' || voyages.ville_arrivee)"
            : "CONCAT(voyages.ville_depart, '-', voyages.ville_arrivee)";

        $avecVoyage = Bagage::from('bagages')
            ->where('bagages.user_id', $user->id)
            ->whereDate('bagages.date_enregistrement', $date)
            ->whereNotNull('bagages.voyage_id')
            ->join('voyages', 'bagages.voyage_id', '=', 'voyages.id')
            ->select(
                DB::raw("COALESCE(voyages.programme, {$concatProgramme}) AS programme"),
                'voyages.ville_depart',
                'voyages.ville_arrivee',
                DB::raw('COUNT(*)                              AS nb_bagages'),
                DB::raw('SUM(bagages.montant)                 AS recettes'),
                DB::raw('SUM(bagages.remise)                  AS remises'),
                DB::raw('SUM(bagages.montant - bagages.remise) AS net')
            )
            ->groupBy(
                'voyages.programme',
                'voyages.ville_depart',
                'voyages.ville_arrivee'
            )
            ->orderByDesc('recettes')
            ->get()
            ->map(fn($r) => [
                'programme'  => $r->programme,
                'nb_bagages' => $r->nb_bagages,
                'recettes'   => $r->recettes,
                'remises'    => $r->remises,
                'net'        => $r->net,
            ])
            ->toArray();

        // Bagages sans voyage lié
        $sansVoyage = Bagage::where('user_id', $user->id)
            ->whereDate('date_enregistrement', $date)
            ->whereNull('voyage_id')
            ->selectRaw('"SANS PROGRAMME" AS programme,
                         COUNT(*)                               AS nb_bagages,
                         SUM(montant)                          AS recettes,
                         SUM(remise)                           AS remises,
                         SUM(montant - remise)                 AS net')
            ->groupByRaw('"SANS PROGRAMME"')
            ->get()
            ->map(fn($r) => [
                'programme'  => $r->programme,
                'nb_bagages' => $r->nb_bagages,
                'recettes'   => $r->recettes,
                'remises'    => $r->remises,
                'net'        => $r->net,
            ])
            ->toArray();

        return array_merge($avecVoyage, $sansVoyage);
    }

    /**
     * État global par JOUR (Admin uniquement).
     *
     * BUG CORRIGÉ :
     *  - Suppression des références aux statuts 'retire' et 'en_transit'
     *    qui n'existent plus dans Bagage::STATUTS.
     *  - Les colonnes bagages_livres et en_transit sont retirées
     *    (statuts supprimés de l'application).
     *  - Seuls 'enregistre' et 'embarque' sont comptabilisés.
     */
    public function getEtatGlobal(string $date = null): array
    {
        $date = $date ?: now()->toDateString();

        $stats = Bagage::whereDate('date_enregistrement', $date)
            ->selectRaw('
                COUNT(*)                                                    AS total_bagages,
                SUM(montant)                                                AS recettes_totales,
                SUM(remise)                                                 AS remises_totales,
                SUM(montant - remise)                                       AS net_total,
                SUM(CASE WHEN statut = "enregistre" THEN 1 ELSE 0 END)    AS nb_enregistres,
                SUM(CASE WHEN statut = "embarque"   THEN 1 ELSE 0 END)    AS nb_embarques
            ')
            ->first();

        $concatNom = DB::getDriverName() === 'sqlite'
            ? "(COALESCE(users.prenom,'') || ' ' || COALESCE(users.nom,''))"
            : "CONCAT(COALESCE(users.prenom,''), ' ', COALESCE(users.nom,''))";

        $parAgent = Bagage::from('bagages')
            ->whereDate('bagages.date_enregistrement', $date)
            ->join('users', 'bagages.user_id', '=', 'users.id')
            ->select(
                'users.id',
                DB::raw("{$concatNom} AS nom_complet"),
                'users.agence',
                DB::raw('COUNT(*)                               AS nb_bagages'),
                DB::raw('SUM(bagages.montant)                  AS recettes'),
                DB::raw('SUM(bagages.remise)                   AS remises'),
                DB::raw('SUM(bagages.montant - bagages.remise) AS net')
            )
            ->groupBy('users.id', 'users.prenom', 'users.nom', 'users.agence')
            ->orderByDesc('recettes')
            ->get();

        // Détail transactions du jour (tous agents)
        $transactions = Bagage::whereDate('date_enregistrement', $date)
            ->with(['client', 'voyage', 'user'])
            ->orderBy('date_enregistrement', 'desc')
            ->get();

        return [
            'stats'        => $stats,
            'parAgent'     => $parAgent,
            'transactions' => $transactions,
            'date'         => $date,
        ];
    }
}
