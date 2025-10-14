<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop the page_feature_image column from pages table.
     * This column was added by mistake - Spatie Media Library
     * handles images via the media table using collection names,
     * so no database column is needed.
     */
    public function up(): void
    {
        if (Schema::hasColumn('pages', 'page_feature_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('page_feature_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Re-add the column if rolling back (though it shouldn't be needed).
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pages', 'page_feature_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('page_feature_image', 80)->unique()->nullable();
            });
        }
    }
};
