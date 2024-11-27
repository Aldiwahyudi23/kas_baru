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
        Schema::create('layouts_forms', function (Blueprint $table) {
            $table->id();
            $table->string('icon_kas');
            $table->string('icon_tabungan');
            $table->string('icon_b_pinjam');
            $table->string('pinjam_pinjam');
            $table->longText('kas_proses');
            $table->longText('tabungan_proses');
            $table->longText('b_pinjam_proses');
            $table->longText('pinjam_proses');
            $table->longText('pinjam_saldo');
            $table->longText('pinjam_penuh');
            $table->longText('pinjam_nunggak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layouts_forms');
    }
};
