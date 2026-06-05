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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bulan');
            $table->year('tahun');
            $table->string('id_pelanggan', 6);
            $table->integer('tagihan');
            $table->enum('status', ['BL', 'LS']);
            $table->date('tgl_bayar');
            $table->timestamps();
            $table->foreign('bulan')->references('id')->on('bulan');
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
