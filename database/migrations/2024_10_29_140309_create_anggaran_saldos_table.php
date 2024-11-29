<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anggaran_saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saldo_id')->constrained('saldos')->onDelete('cascade'); // Referensi pinjaman
            $table->string('type');
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount');
            $table->decimal('saldo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran_saldos');
    }
};
