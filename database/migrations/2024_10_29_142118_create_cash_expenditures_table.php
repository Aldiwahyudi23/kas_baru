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
        Schema::create('cash_expenditures', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('anggaran_id')->constrained('anggarans')->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Jumlah pengeluaran
            $table->longText('description'); // Jenis pengeluaran (misal: Operasional)
            $table->enum('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer', 'Acknowledged', 'rejected'])->default('pending');
            $table->bigInteger('submitted_by')->unsigned(); // Sekretaris yang menginput
            $table->bigInteger('approved_by')->unsigned()->nullable(); // Ketua yang menyetujui
            $table->bigInteger('disbursed_by')->unsigned()->nullable(); // Bendahara yang mencairkan
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('disbursed_date')->nullable();
            $table->string('receipt_path')->nullable(); // Path tanda bukti disburse dari bendahara
            $table->timestamps();

            $table->foreign('submitted_by')->references('id')->on('data_wargas');
            $table->foreign('approved_by')->references('id')->on('data_wargas');
            $table->foreign('disbursed_by')->references('id')->on('data_wargas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_expenditures');
    }
};