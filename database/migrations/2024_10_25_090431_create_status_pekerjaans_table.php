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
        Schema::create('status_pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_warga_id')->constrained('data_wargas')->onDelete('cascade');
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->string('pekerjaan');
            $table->string('jangka_waktu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pekerjaans');
    }
};
