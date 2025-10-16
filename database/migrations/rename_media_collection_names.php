<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update media collection names to be more specific.
     * Renames 'feature_image' to 'page_feature_image' for pages
     * and 'post_feature_image' for posts.
     */
    public function up(): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        // Update media collection names for pages
        // Rename 'feature_image' to 'page_feature_image' for pages
        DB::table('media')
            ->where('model_type', 'Fuelviews\\SabHeroArticles\\Models\\Page')
            ->where('collection_name', 'feature_image')
            ->update(['collection_name' => 'page_feature_image']);

        // Update media collection names for posts
        // Rename 'feature_image' to 'post_feature_image' for posts
        DB::table('media')
            ->where('model_type', 'Fuelviews\\SabHeroArticles\\Models\\Post')
            ->where('collection_name', 'feature_image')
            ->update(['collection_name' => 'post_feature_image']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        // Revert page_feature_image back to feature_image for pages
        DB::table('media')
            ->where('model_type', 'Fuelviews\\SabHeroArticles\\Models\\Page')
            ->where('collection_name', 'page_feature_image')
            ->update(['collection_name' => 'feature_image']);

        // Revert post_feature_image back to feature_image for posts
        DB::table('media')
            ->where('model_type', 'Fuelviews\\SabHeroArticles\\Models\\Post')
            ->where('collection_name', 'post_feature_image')
            ->update(['collection_name' => 'feature_image']);
    }
};
