<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autopilot_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('mode')->default('manual');
            $table->unsignedInteger('posts_per_day')->default(2);
            $table->string('timezone')->default('Asia/Jakarta');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('language')->default('id');
            $table->string('tone')->default('santai');
            $table->boolean('image_enabled')->default(true);
            $table->boolean('auto_publish')->default(false);
            $table->boolean('require_approval')->default(true);
            $table->unsignedInteger('minimum_quality_score')->default(75);
            $table->unsignedInteger('minimum_inventory')->default(5);
            $table->unsignedInteger('target_inventory')->default(14);
            $table->json('categories')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autopilot_settings');
    }
};
