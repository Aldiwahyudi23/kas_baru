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
        Schema::create('status_pernikahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_suami_id')->nullable()->constrained('data_wargas')->onDelete('cascade');
            $table->foreignId('warga_istri_id')->nullable()->constrained('data_wargas')->onDelete('cascade');
            $table->enum('status', ['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'])->default('Belum Menikah');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pernikahans');
    }
};
