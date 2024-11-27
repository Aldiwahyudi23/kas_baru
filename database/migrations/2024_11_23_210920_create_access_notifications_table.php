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
        Schema::create('access_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('data_warga_id')->unsigned(); // ID pengguna yang pembayaran kasnya tercatat
            $table->enum('is_active', [1, 0]);
            $table->timestamps();

            $table->foreign('data_warga_id')->references('id')->on('data_wargas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_notifications');
    }
};
