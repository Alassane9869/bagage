<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('societe', function (Blueprint $table) {
            $table->string('ville')->nullable()->after('adresse');
            $table->string('pays')->default('Mali')->after('ville');
            $table->string('site_web')->nullable()->after('pays');
            $table->string('numero_rccm')->nullable()->after('site_web');   // Registre commerce
            $table->string('numero_ifu')->nullable()->after('numero_rccm'); // Identifiant fiscal
            $table->string('devise')->default('FCFA')->after('numero_ifu');
            $table->string('fuseau_horaire')->default('Africa/Bamako')->after('devise');
            $table->string('logo_path')->nullable()->after('fuseau_horaire');
            $table->text('mentions_recu')->nullable()->after('logo_path'); // texte pied de reçu
        });
    }

    public function down(): void
    {
        Schema::table('societe', function (Blueprint $table) {
            $table->dropColumn([
                'ville','pays','site_web','numero_rccm','numero_ifu',
                'devise','fuseau_horaire','logo_path','mentions_recu',
            ]);
        });
    }
};
