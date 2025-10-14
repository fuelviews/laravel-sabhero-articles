<?php

namespace Fuelviews\SabHeroArticles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sabhero-articles:upgrade {--force : Force re-run migrations even if already executed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade SabHero Articles package schema and data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting SabHero Articles upgrade process...');
        $this->newLine();

        $upgrades = [];
        $filesUpdated = [];

        // Check database state
        $this->info('Checking database state...');
        $hasTable = Schema::hasTable('pages');
        $hasSlug = $hasTable ? Schema::hasColumn('pages', 'slug') : false;
        $hasRoute = $hasTable ? Schema::hasColumn('pages', 'route') : false;

        $this->comment("  → pages table exists: " . ($hasTable ? 'YES' : 'NO'));
        $this->comment("  → pages.slug column exists: " . ($hasSlug ? 'YES' : 'NO'));
        $this->comment("  → pages.route column exists: " . ($hasRoute ? 'YES' : 'NO'));
        $this->newLine();

        // Check if database needs upgrade
        $databaseNeedsUpgrade = false;

        if ($hasSlug && !$hasRoute) {
            $upgrades[] = 'Rename pages.slug column to pages.route';
            $databaseNeedsUpgrade = true;
        } elseif ($hasRoute && !$hasSlug) {
            $this->info('✓ Database already upgraded (route column exists)');
        } elseif ($hasRoute && $hasSlug) {
            $this->warn('⚠ Both slug and route columns exist - inconsistent state!');
            $upgrades[] = 'Fix inconsistent column state';
            $databaseNeedsUpgrade = true;
        }

        // Check if published files need updating (even if database is OK)
        $this->newLine();
        $this->info('Checking published files...');

        $publishedMigration = $this->findPublishedPagesMigration();
        $publishedSeeder = $this->findPublishedPageSeeder();

        $migrationNeedsUpdate = false;
        $seederNeedsUpdate = false;

        if ($publishedMigration) {
            $migContent = File::get($publishedMigration);
            $migrationNeedsUpdate = str_contains($migContent, "->string('slug',");
            $this->comment("  → Migration needs update: " . ($migrationNeedsUpdate ? 'YES' : 'NO'));
        }

        if ($publishedSeeder) {
            $seederContent = File::get($publishedSeeder);
            $seederNeedsUpdate = str_contains($seederContent, "'slug'") ||
                                 str_contains($seederContent, '"slug"') ||
                                 str_contains($seederContent, '$pageBySlug');
            $this->comment("  → Seeder needs update: " . ($seederNeedsUpdate ? 'YES' : 'NO'));

            if ($seederNeedsUpdate) {
                // Show what will be replaced
                $this->comment("    - Found 'slug' references in seeder");
            }
        }

        if ($migrationNeedsUpdate) {
            $upgrades[] = 'Update published create_pages_table migration';
        }

        if ($seederNeedsUpdate) {
            $upgrades[] = 'Update published PageTableSeeder';
        }

        if (empty($upgrades)) {
            $this->info('✓ Your installation is already up to date!');
            return self::SUCCESS;
        }

        // Show what will be upgraded
        $this->warn('The following upgrades will be performed:');
        foreach ($upgrades as $upgrade) {
            $this->line("  • {$upgrade}");
        }
        $this->newLine();

        if (! $this->confirm('Do you want to continue?', true)) {
            $this->error('Upgrade cancelled.');

            return self::FAILURE;
        }

        // Only run database migration if needed
        if (!$databaseNeedsUpgrade) {
            $this->info('Skipping database migration (already upgraded)');
            goto updateFiles;
        }

        // Publish migration to app if not already there
        $this->info('Publishing upgrade migration...');

        $appMigrationsPath = database_path('migrations');
        $packageMigrationFile = __DIR__ . '/../../database/migrations/rename_page_slug_to_route.php';

        $this->comment("  → Package migration: {$packageMigrationFile}");
        $this->comment("  → App migrations: {$appMigrationsPath}");

        // Check if migration file exists in package
        if (!File::exists($packageMigrationFile)) {
            $this->error("  ✗ Migration file not found in package!");
            return self::FAILURE;
        }

        // Create timestamped migration name for app
        $timestamp = date('Y_m_d_His');
        $appMigrationFile = $appMigrationsPath . '/' . $timestamp . '_rename_page_slug_to_route.php';

        // Check if already published (any timestamp)
        $existingMigrations = File::glob($appMigrationsPath . '/*_rename_page_slug_to_route.php');

        if (!empty($existingMigrations)) {
            $this->warn("  ⚠ Migration already published: " . basename($existingMigrations[0]));
        } else {
            // Copy migration to app
            File::copy($packageMigrationFile, $appMigrationFile);
            $this->line("  ✓ Published: " . basename($appMigrationFile));
        }

        // Run migrations
        $this->newLine();
        $this->info('Running migrations...');
        $this->comment('  → Running: php artisan migrate --force');
        $this->newLine();

        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);

        // Show artisan output
        $this->line(Artisan::output());

        $this->newLine();
        $this->comment("  → Migration exit code: {$exitCode}");

        if ($exitCode !== 0) {
            $this->error('Migration failed with exit code: ' . $exitCode);
            return self::FAILURE;
        }

        // Verify the column was renamed
        $this->newLine();
        $this->info('Verifying database changes...');

        // Check current state
        $hasSlugAfter = Schema::hasColumn('pages', 'slug');
        $hasRouteAfter = Schema::hasColumn('pages', 'route');

        $this->comment("  → BEFORE migration:");
        $this->comment("     - pages.slug: " . ($hasSlug ? 'YES' : 'NO'));
        $this->comment("     - pages.route: " . ($hasRoute ? 'YES' : 'NO'));

        $this->comment("  → AFTER migration:");
        $this->comment("     - pages.slug: " . ($hasSlugAfter ? 'YES' : 'NO'));
        $this->comment("     - pages.route: " . ($hasRouteAfter ? 'YES' : 'NO'));

        $this->newLine();

        if ($hasRouteAfter && !$hasSlugAfter) {
            $this->line('  ✓ Column successfully renamed: pages.slug → pages.route');
        } elseif ($hasRouteAfter && $hasSlugAfter) {
            $this->error('  ✗ Both columns exist - migration created route but did not remove slug!');
            return self::FAILURE;
        } elseif (!$hasRouteAfter && $hasSlugAfter) {
            $this->error('  ✗ Column rename failed - slug still exists, route was not created');
            $this->error('  ✗ Check migration output above for errors');
            return self::FAILURE;
        } else {
            $this->error('  ✗ Both columns missing - unexpected state!');
            return self::FAILURE;
        }

        // Update published files
        updateFiles:
        $this->newLine();
        $this->info('Updating published files...');

        // Update migration if needed
        if ($migrationNeedsUpdate && $publishedMigration) {
            $this->comment("  → Updating: " . basename($publishedMigration));
            $this->updatePublishedMigration($publishedMigration);
            $filesUpdated[] = 'migrations/'.basename($publishedMigration);
            $this->line("  ✓ Updated: migrations/".basename($publishedMigration));
        } elseif ($publishedMigration) {
            $this->comment("  ℹ Migration already up to date");
        } else {
            $this->comment("  ℹ No published migration found (skipping)");
        }

        // Update seeder if needed
        if ($seederNeedsUpdate && $publishedSeeder) {
            $this->comment("  → Updating: " . basename($publishedSeeder));
            $this->updatePublishedSeeder($publishedSeeder);
            $filesUpdated[] = 'seeders/'.basename($publishedSeeder);
            $this->line("  ✓ Updated: seeders/".basename($publishedSeeder));
            $this->comment("    - Replaced 'slug' with 'route'");
        } elseif ($publishedSeeder) {
            $this->comment("  ℹ Seeder already up to date");
        } else {
            $this->comment("  ℹ No published seeder found (skipping)");
        }

        $this->newLine();
        $this->info('✓ Upgrade completed successfully!');
        $this->newLine();

        // Show summary
        $this->info('Upgrade summary:');
        foreach ($upgrades as $upgrade) {
            $this->line("  ✓ {$upgrade}");
        }

        if (! empty($filesUpdated)) {
            $this->newLine();
            $this->info('Files updated:');
            foreach ($filesUpdated as $file) {
                $this->line("  ✓ {$file}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Find the published create_pages_table migration in the app.
     */
    protected function findPublishedPagesMigration(): ?string
    {
        $migrationsPath = database_path('migrations');

        if (! File::exists($migrationsPath)) {
            return null;
        }

        $files = File::glob($migrationsPath.'/*_create_pages_table.php');

        return $files[0] ?? null;
    }

    /**
     * Update the published migration file to use 'route' instead of 'slug'.
     */
    protected function updatePublishedMigration(string $filePath): void
    {
        $content = File::get($filePath);

        // Replace slug column definition with route
        $content = preg_replace(
            "/\\\$table->string\('slug',\s*80\)->unique\(\);/",
            "\$table->string('route', 80)->unique();",
            $content
        );

        File::put($filePath, $content);
    }

    /**
     * Find the published PageTableSeeder in the app.
     */
    protected function findPublishedPageSeeder(): ?string
    {
        $seederPath = database_path('seeders/PageTableSeeder.php');

        return File::exists($seederPath) ? $seederPath : null;
    }

    /**
     * Update the published seeder file to use 'route' instead of 'slug'.
     */
    protected function updatePublishedSeeder(string $filePath): void
    {
        $content = File::get($filePath);

        // Replace 'slug' => with 'route' =>
        $content = str_replace("'slug' =>", "'route' =>", $content);

        // Replace ->where('slug', with ->where('route',
        $content = str_replace("->where('slug',", "->where('route',", $content);

        // Replace variable names $pageBySlug with $pageByRoute
        $content = str_replace('$pageBySlug', '$pageByRoute', $content);

        // Replace comments about slug with route
        $content = str_replace('slug OR title', 'route OR title', $content);
        $content = str_replace('finding by slug', 'finding by route', $content);
        $content = str_replace('neither slug nor title', 'neither route nor title', $content);
        $content = str_replace('actually needs the route name, not the slug', 'Laravel route name for this page', $content);

        File::put($filePath, $content);
    }
}
