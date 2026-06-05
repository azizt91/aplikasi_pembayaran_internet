<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan');
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('info'); // info, tagihan, reminder, promo
            $table->json('data')->nullable(); // additional data (tagihan_id, etc)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('id_pelanggan');
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_notifications');
    }
};
