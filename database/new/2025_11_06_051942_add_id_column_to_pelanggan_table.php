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
        // Step 1: Drop all foreign keys that reference pelanggan (with error handling)
        try {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->dropForeign('tagihan_id_pelanggan_foreign');
            });
        } catch (\Exception $e) {}
        
        try {
            Schema::table('wifi_change_history', function (Blueprint $table) {
                $table->dropForeign('wifi_change_history_id_pelanggan_foreign');
            });
        } catch (\Exception $e) {}
        
        try {
            Schema::table('wifi_settings', function (Blueprint $table) {
                $table->dropForeign('wifi_settings_id_pelanggan_foreign');
            });
        } catch (\Exception $e) {}
        
        // Step 2: Drop foreign key from pelanggan itself (if exists)
        try {
            Schema::table('pelanggan', function (Blueprint $table) {
                $table->dropForeign('pelanggan_id_paket_foreign');
            });
        } catch (\Exception $e) {}
        
        // Step 3: Drop existing primary key on id_pelanggan
        DB::statement('ALTER TABLE pelanggan DROP PRIMARY KEY');
        
        // Step 4: Add new id column as auto increment primary key
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->id()->first();
        });
        
        // Step 5: Update existing records to have sequential IDs
        DB::statement('SET @count = 0');
        DB::statement('UPDATE pelanggan SET id = @count:= @count + 1 ORDER BY id_pelanggan');
        
        // Step 6: Make id_pelanggan unique (not primary key)
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->unique('id_pelanggan');
        });
        
        // Step 7: Re-add all foreign key constraints
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->foreign('id_paket')->references('id_paket')->on('paket');
        });
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan');
        });
        Schema::table('wifi_change_history', function (Blueprint $table) {
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan');
        });
        Schema::table('wifi_settings', function (Blueprint $table) {
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
