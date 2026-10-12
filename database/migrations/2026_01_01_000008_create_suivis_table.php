<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suivis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bagage_id')->constrained('bagages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('localisation')->nullable();
            $table->string('statut');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index('bagage_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suivis');
    }
};
