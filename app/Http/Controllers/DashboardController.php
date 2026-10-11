<?php

namespace App\Http\Controllers;

use App\Models\Bagage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $debut = $request->get('debut', now()->startOfYear()->toDateString());
        $fin   = $request->get('fin',   now()->toDateString());

        // ── Stat cards ────────────────────────────────────────
        $totalEnregistres = Bagage::whereBetween('date_enregistrement',
            [$debut . ' 00:00:00', $fin . ' 23:59:59'])->count();

        $totalEmbarques = Bagage::whereBetween('date_enregistrement',
            [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->where('statut', 'embarque')->count();

        $tauxEmbarquement = $totalEnregistres > 0
            ? round(($totalEmbarques / $totalEnregistres) * 100, 1)
            : 0;

        $chiffreAffaires = Bagage::whereBetween('date_enregistrement',
            [$debut . ' 00:00:00', $fin . ' 23:59:59'])->sum('montant');

        // ── Évolution mensuelle ────────────────────────────────
        $isSqlite = DB::getDriverName() === 'sqlite';
        $moisCol  = $isSqlite ? "strftime('%Y-%m', date_enregistrement)" : "DATE_FORMAT(date_enregistrement, '%Y-%m')";
        $labelCol = $isSqlite ? "strftime('%m/%Y', date_enregistrement)" : "DATE_FORMAT(date_enregistrement, '%b %Y')";

        $evolutionRaw = Bagage::whereBetween('date_enregistrement',
            [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->select(
                DB::raw("{$moisCol} as mois"),
                DB::raw("{$labelCol} as mois_label"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "embarque" THEN 1 ELSE 0 END) as embarques'),
                DB::raw('SUM(montant) as recettes')
            )
            ->groupBy('mois', 'mois_label')
            ->orderBy('mois')
            ->get();

        // ── Recettes par destination ───────────────────────────
        $parDestination = Bagage::whereBetween('bagages.date_enregistrement',
            [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->join('voyages', 'bagages.voyage_id', '=', 'voyages.id')
            ->select(
                'voyages.ville_arrivee',
                DB::raw('COUNT(*) as nb_bagages'),
                DB::raw('SUM(bagages.montant) as recettes')
            )
            ->groupBy('voyages.ville_arrivee')
            ->orderByDesc('recettes')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'debut', 'fin',
            'totalEnregistres', 'totalEmbarques',
            'tauxEmbarquement', 'chiffreAffaires',
            'evolutionRaw', 'parDestination'
        ));
    }
}
