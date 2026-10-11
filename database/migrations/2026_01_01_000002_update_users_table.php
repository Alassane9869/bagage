<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('prenom')->nullable()->after('name');
            $table->string('nom')->nullable()->after('prenom');
            $table->string('telephone')->nullable()->after('email');
            $table->foreignId('role_id')->nullable()->constrained('roles')->after('telephone');
            $table->string('agence')->nullable()->after('role_id');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('agence');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['prenom', 'nom', 'telephone', 'role_id', 'agence', 'statut']);
        });
    }
};
