<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rename the slug column to route in the pages table.
     * The column stores Laravel route names, not URL slugs,
     * so 'route' is a more semantically accurate name.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        $hasSlug = Schema::hasColumn('pages', 'slug');
        $hasRoute = Schema::hasColumn('pages', 'route');

        if ($hasSlug && ! $hasRoute) {
            Schema::table('pages', function (Blueprint $table) {
                $table->renameColumn('slug', 'route');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Rename the route column back to slug.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pages', 'route') && ! Schema::hasColumn('pages', 'slug')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->renameColumn('route', 'slug');
            });
        }
    }
};
