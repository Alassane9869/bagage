<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date_caisse');
            $table->unsignedInteger('total_bagages')->default(0);
            $table->decimal('recettes', 12, 2)->default(0);
            $table->decimal('remises', 12, 2)->default(0);
            $table->decimal('net', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date_caisse']);
            $table->index('date_caisse');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};
