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
            $table->string('submitted_by');
            $table->enum('payment_method', ['transfer', 'cash'])->nullable();
            $table->enum('status', ['pending', 'Berhasil', 'Gagal'])->default('pending'); // Status perpanjangan
            $table->decimal('buying_price', 10, 2)->nullable(); //harga belo
            $table->decimal('price', 10, 2); //harga jual
            $table->boolean('is_deposited')->default(false)->nullable();
            $table->bigInteger('deposit_id')->unsigned()->nullable();
            $table->timestamp('deadline_date')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product_konters')->onDelete('set null');
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
