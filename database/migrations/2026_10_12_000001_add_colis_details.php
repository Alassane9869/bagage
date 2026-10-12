<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->string('nature')->nullable()->after('numero_colis');
            $table->string('valeur')->nullable()->after('nature');
            $table->decimal('prix', 10, 2)->nullable()->after('valeur');
        });
    }

    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropColumn(['nature', 'valeur', 'prix']);
        });
    }
};
