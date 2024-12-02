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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->bigInteger('submitted_by')->unsigned()->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->bigInteger('confirmed_by')->unsigned()->nullable();
            $table->timestamp('confirmation_date')->nullable();
            $table->string('receipt_path')->nullable(); // Menyimpan path untuk bukti transfer
            $table->longText('description');
            $table->timestamps();

            $table->foreign('submitted_by')->references('id')->on('data_wargas')->onDelete('set null'); // Menyimpan referensi ke pengguna yang input
            $table->foreign('confirmed_by')->references('id')->on('data_wargas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
