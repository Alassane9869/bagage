<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bagages', function (Blueprint $table) {
            $table->id();
            $table->string('code_bagage')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('voyage_id')->nullable()->constrained('voyages')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('nombre_colis')->default(1);
            $table->string('nature')->nullable();
            $table->decimal('poids', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2)->default(0);
            $table->decimal('remise', 10, 2)->default(0);
            $table->string('numero_billet')->nullable();
            $table->enum('statut', [
                'enregistre',
                'embarque',
                'en_transit',
                'arrive',
                'retire',
                'perdu',
                'endommage'
            ])->default('enregistre');
            $table->timestamp('date_enregistrement')->useCurrent();
            $table->timestamps();

            $table->index('code_bagage');
            $table->index('client_id');
            $table->index('voyage_id');
            $table->index('statut');
            $table->index('date_enregistrement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bagages');
    }
};
