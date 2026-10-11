<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ticket')->unique();
            $table->foreignId('bagage_id')->nullable()->constrained('bagages')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('voyage_id')->nullable()->constrained('voyages')->nullOnDelete();
            $table->decimal('montant', 10, 2)->default(0);
            $table->timestamp('date_emission')->useCurrent();
            $table->text('qr_code')->nullable();
            $table->timestamps();

            $table->index('numero_ticket');
            $table->index('bagage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
