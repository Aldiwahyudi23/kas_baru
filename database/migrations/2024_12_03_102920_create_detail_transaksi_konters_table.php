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
        Schema::create('detail_transaksi_konters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaksi_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('no_hp')->nullable();
            $table->string('no_listrik')->nullable();
            $table->string('token_code')->nullable();
            $table->timestamps();

            $table->foreign('transaksi_id')->references('id')->on('transaksi_konters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_konters');
    }
};
