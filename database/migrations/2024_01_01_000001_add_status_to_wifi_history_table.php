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
        if (!Schema::hasTable('wifi_change_history')) {
            Schema::create('wifi_change_history', function (Blueprint $table) {
                $table->id();
                $table->string('id_pelanggan');
                $table->string('type');
                $table->string('description')->nullable();
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->string('changed_by')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('status')->default('success');
                $table->timestamps();
            });
        } else {
            Schema::table('wifi_change_history', function (Blueprint $table) {
                if (!Schema::hasColumn('wifi_change_history', 'description')) {
                    $table->string('description')->nullable()->after('type');
                }
                if (!Schema::hasColumn('wifi_change_history', 'status')) {
                    $table->string('status')->default('success')->after('user_agent');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_change_history');
    }
};
