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
            $table->bigInteger('notification_id')->unsigned(); // ID pengguna yang pembayaran kasnya tercatat
            $table->bigInteger('data_warga_id')->unsigned(); // ID pengguna yang pembayaran kasnya tercatat
            $table->boolean('is_active')->default(false)->nullable();
            $table->timestamps();

            $table->foreign('notification_id')->references('id')->on('data_notifications')->onDelete('cascade');
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
