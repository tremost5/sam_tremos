<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('facebook_page_id')->nullable();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('caption')->nullable();
            $table->text('hashtags')->nullable();
            $table->string('image_path')->nullable();
            $table->text('image_prompt')->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->unsignedTinyInteger('quality_score')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('facebook_post_id')->nullable();
            $table->string('status')->default('draft');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
