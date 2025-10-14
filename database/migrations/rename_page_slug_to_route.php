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
            echo "  ⚠ pages table does not exist - skipping\n";
            return;
        }

        $hasSlug = Schema::hasColumn('pages', 'slug');
        $hasRoute = Schema::hasColumn('pages', 'route');

        echo "  → Before: slug={$hasSlug}, route={$hasRoute}\n";

        if ($hasSlug && ! $hasRoute) {
            echo "  → Renaming column slug to route...\n";

            try {
                Schema::table('pages', function (Blueprint $table) {
                    $table->renameColumn('slug', 'route');
                });

                echo "  ✓ Column renamed successfully\n";
            } catch (\Exception $e) {
                echo "  ✗ Error: " . $e->getMessage() . "\n";
                throw $e;
            }
        } elseif ($hasRoute) {
            echo "  ℹ Column already renamed - skipping\n";
        } else {
            echo "  ⚠ Unexpected state - both columns missing?\n";
        }

        // Verify after
        $hasSlugAfter = Schema::hasColumn('pages', 'slug');
        $hasRouteAfter = Schema::hasColumn('pages', 'route');
        echo "  → After: slug={$hasSlugAfter}, route={$hasRouteAfter}\n";
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
