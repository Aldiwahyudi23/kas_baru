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
        Schema::create('transaksi_konters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->bigInteger('konter_detail_id')->unsigned()->nullable();
            $table->string('submitted_by');
            $table->enum('payment_status', ['Langsung', 'Hutang']);
            $table->enum('payment_method', ['transfer', 'cash'])->nullable();
            $table->enum('status', ['pending', 'Proses', 'Berhasil', 'Selesai', 'Gagal'])->default('pending'); // Status perpanjangan
            $table->decimal('buying_price', 10, 2)->nullable(); //harga belo
            $table->decimal('price', 10, 2); //harga jual
            $table->decimal('diskon', 10, 2)->nullable(); //harga jual
            $table->decimal('invoice', 10, 2)->nullable(); //harga jual
            $table->decimal('margin', 10, 2)->nullable(); //harga jual
            $table->timestamp('deadline_date')->nullable(); // Waktu batas akhir
            $table->boolean('is_deposited')->default(false)->nullable(); //jika uang sudah masuk Bank kas bersetatus true
            $table->bigInteger('deposit_id')->unsigned()->nullable();
            $table->bigInteger('warga_id')->unsigned()->nullable();
            $table->bigInteger('confirmed_by')->unsigned()->nullable();
            $table->timestamp('confirmation_date')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product_konters')->onDelete('set null');
            $table->foreign('konter_detail_id')->references('id')->on('detail_transaksi_konters')->onDelete('set null');
            $table->foreign('warga_id')->references('id')->on('data_wargas')->onDelete('set null');
            $table->foreign('confirmed_by')->references('id')->on('data_wargas')->onDelete('set null');
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_konters');
    }
};