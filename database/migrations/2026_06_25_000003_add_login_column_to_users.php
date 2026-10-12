<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Colonne login libre (pas email) — unique
            $table->string('login')->nullable()->unique()->after('email');
        });

        // Pré-remplir login avec la partie avant @ de l'email existant
        DB::statement("
            UPDATE users
            SET login = CASE
                WHEN email LIKE '%@%' THEN SUBSTRING_INDEX(email, '@', 1)
                ELSE email
            END
        ");

        // Rendre le login NOT NULL après remplissage
        Schema::table('users', function (Blueprint $table) {
            $table->string('login')->nullable(false)->change();
        });

        // Rendre email nullable (plus obligatoire)
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login');
            $table->string('email')->nullable(false)->change();
        });
    }
};
