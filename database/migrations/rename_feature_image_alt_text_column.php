<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rename feature_image_alt_text to post_feature_image_alt_text in posts table.
     * This provides more specific naming to distinguish post images from page images.
     */
    public function up(): void
    {
        $postsTable = config('sabhero-articles.tables.prefix').'posts';

        if (! Schema::hasTable($postsTable)) {
            return;
        }

        $hasOldColumn = Schema::hasColumn($postsTable, 'feature_image_alt_text');
        $hasNewColumn = Schema::hasColumn($postsTable, 'post_feature_image_alt_text');

        if ($hasOldColumn && ! $hasNewColumn) {
            Schema::table($postsTable, function (Blueprint $table) {
                $table->renameColumn('feature_image_alt_text', 'post_feature_image_alt_text');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $postsTable = config('sabhero-articles.tables.prefix').'posts';

        if (Schema::hasColumn($postsTable, 'post_feature_image_alt_text') && ! Schema::hasColumn($postsTable, 'feature_image_alt_text')) {
            Schema::table($postsTable, function (Blueprint $table) {
                $table->renameColumn('post_feature_image_alt_text', 'feature_image_alt_text');
            });
        }
    }
};
