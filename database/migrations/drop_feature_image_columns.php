<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop feature_image and page_feature_image columns from pages table.
     * Spatie Media Library handles images via the media table using collection names,
     * so no database column is needed.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'feature_image')) {
                $table->dropColumn('feature_image');
            }
            if (Schema::hasColumn('pages', 'page_feature_image')) {
                $table->dropColumn('page_feature_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible - media library handles images now
    }
};
