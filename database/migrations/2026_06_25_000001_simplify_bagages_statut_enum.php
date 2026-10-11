<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ramener tous les statuts supprimés vers 'embarque' pour éviter les erreurs
        DB::table('bagages')
            ->whereIn('statut', ['en_transit', 'arrive', 'retire', 'perdu', 'endommage'])
            ->update(['statut' => 'embarque']);

        // Modifier l'ENUM pour MySQL (SQLite ne gère pas les ENUMs / MODIFY COLUMN)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE bagages
                MODIFY COLUMN statut ENUM('enregistre','embarque') NOT NULL DEFAULT 'enregistre'
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE bagages
                MODIFY COLUMN statut ENUM(
                    'enregistre','embarque','en_transit','arrive','retire','perdu','endommage'
                ) NOT NULL DEFAULT 'enregistre'
            ");
        }
    }
};
