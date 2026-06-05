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
        Schema::create('genieacs_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('genieacs_settings')->insert([
            [
                'setting_key' => 'genieacs_enabled',
                'setting_value' => 'false',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'genieacs_url',
                'setting_value' => '',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'genieacs_username',
                'setting_value' => '',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'genieacs_password',
                'setting_value' => '',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genieacs_settings');
    }
};
