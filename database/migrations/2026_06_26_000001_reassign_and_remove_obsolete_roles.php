<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $agentBagages = DB::table('roles')->where('slug', 'agent_bagages')->first();
        $controleur   = DB::table('roles')->where('slug', 'controleur')->first();

        if (!$agentBagages || !$controleur) {
            return; // sécurité : ne rien faire si les rôles cibles n'existent pas
        }

        // Réaffecte les agent_billetterie → agent_bagages (mêmes permissions fonctionnelles)
        DB::table('users')
            ->whereIn('role_id', function ($q) {
                $q->select('id')->from('roles')->where('slug', 'agent_billetterie');
            })
            ->update(['role_id' => $agentBagages->id]);

        // Réaffecte responsable et superviseur → controleur (consultation uniquement)
        DB::table('users')
            ->whereIn('role_id', function ($q) {
                $q->select('id')->from('roles')->whereIn('slug', ['responsable', 'superviseur']);
            })
            ->update(['role_id' => $controleur->id]);

        // Maintenant que plus aucun utilisateur ne référence ces rôles, on peut les supprimer
        DB::table('roles')
            ->whereIn('slug', ['responsable', 'agent_billetterie', 'superviseur'])
            ->delete();
    }

    public function down(): void
    {
        // Recrée les rôles supprimés (sans restaurer les affectations d'utilisateurs)
        $now = now();
        DB::table('roles')->insert([
            ['nom' => 'Responsable',       'slug' => 'responsable',       'permissions' => json_encode(['caisse','rapports','consultation']), 'created_at' => $now, 'updated_at' => $now],
            ['nom' => 'Agent Billetterie', 'slug' => 'agent_billetterie', 'permissions' => json_encode(['bagages','caisse']),                  'created_at' => $now, 'updated_at' => $now],
            ['nom' => 'Superviseur',       'slug' => 'superviseur',       'permissions' => json_encode(['caisse','rapports']),                  'created_at' => $now, 'updated_at' => $now],
        ]);
    }
};
