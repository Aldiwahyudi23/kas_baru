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
        Schema::create('anggaran_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggaran_id')->constrained('anggarans')->onDelete('cascade');
            $table->string('label_anggaran')->nullable();
            $table->string('catatan_anggaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran_settings');
    }
};
