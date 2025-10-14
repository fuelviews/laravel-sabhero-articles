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
        // Rename feature_image_alt_text to post_feature_image_alt_text in posts table
        if (Schema::hasColumn(config('sabhero-articles.tables.prefix').'posts', 'feature_image_alt_text')) {
            Schema::table(config('sabhero-articles.tables.prefix').'posts', function (Blueprint $table) {
                $table->renameColumn('feature_image_alt_text', 'post_feature_image_alt_text');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert post_feature_image_alt_text back to feature_image_alt_text
        if (Schema::hasColumn(config('sabhero-articles.tables.prefix').'posts', 'post_feature_image_alt_text')) {
            Schema::table(config('sabhero-articles.tables.prefix').'posts', function (Blueprint $table) {
                $table->renameColumn('post_feature_image_alt_text', 'feature_image_alt_text');
            });
        }
    }
};
