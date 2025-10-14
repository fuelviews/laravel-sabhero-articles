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
        // Rename feature_image to page_feature_image in pages table
        if (Schema::hasColumn('pages', 'feature_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->renameColumn('feature_image', 'page_feature_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert page_feature_image back to feature_image
        if (Schema::hasColumn('pages', 'page_feature_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->renameColumn('page_feature_image', 'feature_image');
            });
        }
    }
};
