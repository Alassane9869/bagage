<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voyages', function (Blueprint $table) {
            $table->id();
            $table->string('ville_depart');
            $table->string('ville_arrivee');
            $table->date('date_depart');
            $table->string('heure_depart')->nullable();
            $table->string('programme')->nullable();
            $table->string('numero_bus')->nullable();
            $table->enum('statut_voyage', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->timestamps();

            $table->index(['ville_depart', 'ville_arrivee']);
            $table->index('date_depart');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
