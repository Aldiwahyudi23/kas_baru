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
        Schema::create('product_konters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kategori_id')->unsigned()->nullable();
            $table->bigInteger('provider_id')->unsigned()->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('buying_price', 10, 2); //harga belo
            $table->decimal('price', 10, 2); //harga jual
            $table->decimal('price1', 10, 2); //harga jual
            $table->decimal('price2', 10, 2); //harga jual
            $table->decimal('price3', 10, 2); //harga jual
            $table->decimal('price4', 10, 2); //harga jual
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('kategori_konters')->onDelete('set null');
            $table->foreign('provider_id')->references('id')->on('provider_konters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_konters');
    }
};
