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
        Schema::table('pelanggan', function (Blueprint $table) {
            // Tambah kolom IP PPPoE/Static
            $table->string('ip_address', 50)->nullable()->after('id_paket');
            
            // Hapus kolom jatuh_tempo
            $table->dropColumn('jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            // Kembalikan kolom jatuh_tempo
            $table->integer('jatuh_tempo')->nullable();
            
            // Hapus kolom ip_address
            $table->dropColumn('ip_address');
        });
    }
};
