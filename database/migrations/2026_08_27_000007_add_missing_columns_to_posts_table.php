<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'idea')) {
                $table->text('idea')->nullable()->after('title');
            }

            if (! Schema::hasColumn('posts', 'engagement_question')) {
                $table->text('engagement_question')->nullable()->after('hashtags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'engagement_question')) {
                $table->dropColumn('engagement_question');
            }

            if (Schema::hasColumn('posts', 'idea')) {
                $table->dropColumn('idea');
            }
        });
    }
};
