<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bagage_id')->constrained('bagages')->cascadeOnDelete();
            $table->string('numero_colis'); // ex: BG-2026-00001-C1
            $table->timestamps();

            $table->index('bagage_id');
            $table->unique('numero_colis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colis');
    }
};
