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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
             // Relasi ke bank_accounts
            $table->foreignId('bank_account_id')
                  ->constrained('bank_accounts')
                  ->onDelete('cascade');
            
            // Relasi ke saldos
            $table->foreignId('saldo_id')
                  ->constrained('saldos')
                  ->onDelete('cascade')->nullable();
            $table->decimal('balance', 15, 2)->comment('Saldo setelah transaksi');
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
