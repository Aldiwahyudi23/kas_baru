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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('anggaran_id')->constrained('anggarans')->onDelete('cascade'); // anggaran dengan alokasi pinjaman
            $table->foreignId('data_warga_id')->constrained('data_wargas')->onDelete('cascade'); // Warga peminjam
            $table->decimal('loan_amount', 12, 2); // Jumlah pinjaman
            $table->decimal('remaining_balance', 12, 2); // Sisa saldo setelah cicilan
            $table->decimal('overpayment_balance', 12, 2)->default(0); // Saldo kelebihan pembayaran
            $table->longText('description');
            $table->enum('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer', 'Acknowledged', 'In Repayment', 'Paid in Full', 'rejected'])->default('pending');
            $table->bigInteger('submitted_by')->unsigned(); // Sekretaris yang menginput
            $table->bigInteger('approved_by')->unsigned()->nullable(); // Ketua yang menyetujui
            $table->bigInteger('disbursed_by')->unsigned()->nullable(); // Bendahara yang mencairkan
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('disbursed_date')->nullable();
            $table->timestamp('deadline_date')->nullable();
            $table->string('disbursement_receipt_path')->nullable(); // Tanda bukti pencairan dari bendahara
            $table->timestamps();

            $table->foreign('submitted_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('disbursed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};