<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('facebook');
            $table->text('access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status')->default('disconnected');
            $table->timestamps();
        });

        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('facebook_id');
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('access_token')->nullable();
            $table->json('permissions')->nullable();
            $table->string('status')->default('connected');
            $table->boolean('selected')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
        Schema::dropIfExists('facebook_accounts');
    }
};
