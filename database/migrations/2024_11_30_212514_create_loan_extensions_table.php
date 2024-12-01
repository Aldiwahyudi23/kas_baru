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
        Schema::create('loan_extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id'); // Referensi ke tabel loans
            $table->unsignedBigInteger('new_loan_id')->nullable(); // Referensi ke tabel loans
            $table->date('extension_date'); // Tanggal jatuh tempo setelah perpanjangan
            $table->string('reason')->nullable(); // Alasan perpanjangan (opsional)
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status perpanjangan
            $table->bigInteger('submitted_by')->unsigned()->nullable(); // User yang memproses (ketua, bendahara, dll.)
            $table->timestamps();

            // Relasi ke tabel loans
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->foreign('new_loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('data_wargas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_extensions');
    }
};
