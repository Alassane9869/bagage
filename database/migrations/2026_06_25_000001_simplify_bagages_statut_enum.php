<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ramener tous les statuts supprimés vers 'embarque' pour éviter les erreurs
        DB::statement("
            UPDATE bagages
            SET statut = 'embarque'
            WHERE statut IN ('en_transit', 'arrive', 'retire', 'perdu', 'endommage')
        ");

        // Modifier l'ENUM pour ne garder que les 2 statuts
        DB::statement("
            ALTER TABLE bagages
            MODIFY COLUMN statut ENUM('enregistre','embarque') NOT NULL DEFAULT 'enregistre'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE bagages
            MODIFY COLUMN statut ENUM(
                'enregistre','embarque','en_transit','arrive','retire','perdu','endommage'
            ) NOT NULL DEFAULT 'enregistre'
        ");
    }
};
