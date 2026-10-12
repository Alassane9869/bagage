<?php

namespace Database\Seeders;

use App\Models\Bagage;
use App\Models\Client;
use App\Models\Colis;
use App\Models\Role;
use App\Models\Societe;
use App\Models\Suivi;
use App\Models\User;
use App\Models\Voyage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Rôles — 3 rôles applicatifs uniquement ──────────────
        $roles = [
            ['nom' => 'Administrateur', 'slug' => 'administrateur', 'permissions' => ['*']],
            ['nom' => 'Agent Bagages',  'slug' => 'agent_bagages',  'permissions' => ['bagages', 'embarquement', 'caisse']],
            ['nom' => 'Contrôleur',     'slug' => 'controleur',     'permissions' => ['consultation', 'suivi']],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], [
                'nom'         => $r['nom'],
                'permissions' => json_encode($r['permissions']),
            ]);
        }

        $roleAdmin = Role::where('slug', 'administrateur')->first();
        $roleAgent = Role::where('slug', 'agent_bagages')->first();

        // ── Utilisateurs de démonstration ────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@diarratransport.ml'], [
            'name'     => 'DIARRA ADMIN',
            'prenom'   => 'Diarra',
            'nom'      => 'Admin',
            'login'    => 'admin',
            'password' => Hash::make('admin123'),
            'role_id'  => $roleAdmin->id,
            'agence'   => 'Siège',
            'statut'   => 'actif',
        ]);

        $yacou = User::firstOrCreate(['email' => 'yacou.diakite@diarratransport.ml'], [
            'name'     => 'YACOU DIAKITE',
            'prenom'   => 'Yacou',
            'nom'      => 'Diakite',
            'login'    => 'yacou.diakite',
            'password' => Hash::make('agent123'),
            'role_id'  => $roleAgent->id,
            'agence'   => 'Bamako Central',
            'statut'   => 'actif',
        ]);

        // ── Société ───────────────────────────────────────────────
        Societe::firstOrCreate(['id' => 1], [
            'raison_sociale'    => 'DIARRA TRANSPORT',
            'telephone'         => '(223) 20802142',
            'email'             => 'contact@diarratransport.ml',
            'adresse'           => 'Près de la maternité Sogoniko – Bamako, Mali',
            'guichet_principal' => '20802142',
        ]);

        // ── Voyages ───────────────────────────────────────────────
        $voyagesData = [
            ['Bamako','Kayes',   '2026-06-03','08:00','BAMAKO-KAYES 08H',   'BM-001'],
            ['Bamako','Kayes',   '2026-06-03','10:00','BAMAKO-KAYES 10H',   'BM-002'],
            ['Bamako','Dakar',   '2026-06-03','06:00','BAMAKO-DAKAR 06H',   'BM-003'],
            ['Bamako','Sikasso', '2026-06-03','07:00','BAMAKO-SIKASSO 07H', 'SK-001'],
            ['Bamako','Abidjan', '2026-06-03','08:00','BAMAKO-ABIDJAN 08H', 'AB-001'],
        ];

        $voyages = [];
        foreach ($voyagesData as $v) {
            $voyages[] = Voyage::firstOrCreate(
                ['programme' => $v[4], 'date_depart' => $v[2]],
                [
                    'ville_depart'  => $v[0],
                    'ville_arrivee' => $v[1],
                    'heure_depart'  => $v[3],
                    'numero_bus'    => $v[5],
                    'statut_voyage' => 'planifie',
                ]
            );
        }

        // ── Clients ───────────────────────────────────────────────
        $clientsData = [
            ['Mamadou',  'Coulibaly', '+223 76 11 22 33'],
            ['Awa',      'Traoré',    '+223 65 44 55 66'],
            ['Oumar',    'Diallo',    '+223 79 77 88 99'],
            ['Mariam',   'Keïta',     '+223 66 12 34 56'],
            ['Ibrahim',  'Sanogo',    '+223 70 98 76 54'],
            ['Fatoumata','Cissé',     '+223 75 33 22 11'],
            ['Boubacar', 'Bah',       '+223 61 55 44 33'],
            ['Aminata',  'Sidibé',    '+223 72 66 77 88'],
            ['Moussa',   'Dembélé',   '+223 77 55 66 44'],
            ['Rokia',    'Fofana',    '+223 63 44 33 22'],
        ];

        $clients = [];
        foreach ($clientsData as $c) {
            $clients[] = Client::firstOrCreate(
                ['telephone' => $c[2]],
                ['prenom' => $c[0], 'nom' => $c[1]]
            );
        }

        // ── Bagages exemple BG-2026-00001 à BG-2026-00010 ────────
        $natures  = ['Valise', 'Sac', 'Carton', 'Colis fragile', 'Marchandise'];
        $statuts  = ['enregistre', 'embarque', 'enregistre', 'embarque', 'enregistre',
                     'enregistre', 'embarque', 'enregistre', 'embarque', 'enregistre'];
        $agents = [$yacou, $yacou, $yacou, $yacou, $yacou,
                   $yacou, $yacou, $yacou, $yacou, $yacou];

        for ($i = 1; $i <= 10; $i++) {
            $code = 'BG-2026-' . str_pad($i, 5, '0', STR_PAD_LEFT);

            if (Bagage::where('code_bagage', $code)->exists()) continue;

            $nb      = rand(1, 3);
            $montant = ($nb * rand(3000, 8000));
            $remise  = ($i % 3 === 0) ? 2000 : 0;
            $voyage  = $voyages[($i - 1) % count($voyages)];
            $client  = $clients[$i - 1];
            $agent   = $agents[$i - 1];

            $bagage = Bagage::create([
                'code_bagage'        => $code,
                'client_id'          => $client->id,
                'voyage_id'          => $voyage->id,
                'user_id'            => $agent->id,
                'nombre_colis'       => $nb,
                'nature'             => $natures[$i % count($natures)],
                'poids'              => round(rand(5, 30) + 0.5, 1),
                'description'        => 'Effets personnels',
                'montant'            => $montant,
                'remise'             => $remise,
                'numero_billet'      => '2819' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/26',
                'statut'             => $statuts[$i - 1],
                'date_enregistrement'=> now()->subHours(rand(0, 72)),
            ]);

            // Colis
            for ($c = 1; $c <= $nb; $c++) {
                Colis::firstOrCreate(
                    ['numero_colis' => $code . '-C' . $c],
                    ['bagage_id' => $bagage->id]
                );
            }

            // Suivi initial
            Suivi::create([
                'bagage_id'    => $bagage->id,
                'user_id'      => $agent->id,
                'localisation' => 'Bamako',
                'statut'       => 'enregistre',
                'commentaire'  => 'Enregistrement initial',
            ]);

            // Suivi embarqué si applicable
            if (in_array($statuts[$i-1], ['embarque'])) {
                Suivi::create([
                    'bagage_id'    => $bagage->id,
                    'user_id'      => $agent->id,
                    'localisation' => 'Bamako – Bus ' . ($voyage->numero_bus ?? 'BM-001'),
                    'statut'       => 'embarque',
                    'commentaire'  => 'Chargé dans le bus',
                    'created_at'   => now()->subHours(rand(1, 48)),
                    'updated_at'   => now()->subHours(rand(1, 48)),
                ]);
            }
        }
    }
}
