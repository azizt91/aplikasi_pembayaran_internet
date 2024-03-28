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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id_pelanggan', 6)->primary();
            $table->string('nama', 20);
            $table->text('alamat');
            $table->string('whatsapp', 15);
            $table->string('email', 30);
            $table->string('password', 15);
            $table->string('level', 5);
            $table->string('id_paket', 6);
            $table->timestamps();
            $table->foreign('id_paket')->references('id_paket')->on('paket');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
