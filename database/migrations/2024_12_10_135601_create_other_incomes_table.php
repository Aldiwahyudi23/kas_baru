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
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->bigInteger('anggaran_id')->unsigned(); // ID pengguna yang pembayaran kasnya tercatat
            $table->bigInteger('submitted_by')->unsigned()->nullable(); // ID pengguna yang menginput pembayaran jika berbeda
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['transfer', 'cash']);
            $table->enum('status', ['pending', 'process', 'confirmed', 'rejected'])->default('pending');
            $table->bigInteger('confirmed_by')->unsigned()->nullable();
            $table->timestamp('confirmation_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->bigInteger('deposit_id')->unsigned()->nullable();
            $table->boolean('is_deposited')->default(false);
            $table->string('transfer_receipt_path')->nullable(); // Menyimpan path untuk bukti transfer
            $table->longText('description');
            $table->timestamps();

            $table->foreign('anggaran_id')->references('id')->on('anggarans')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('data_wargas')->onDelete('set null'); // Menyimpan referensi ke pengguna yang input
            $table->foreign('confirmed_by')->references('id')->on('data_wargas')->onDelete('set null');
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_incomes');
    }
};
