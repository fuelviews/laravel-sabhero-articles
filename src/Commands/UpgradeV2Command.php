<?php

namespace Fuelviews\SabHeroArticles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UpgradeV2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sabhero-articles:upgrade-v2 {--force : Force re-run migrations and seeder even if already executed}';

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

        // Pages table checks
        $hasTable = Schema::hasTable('pages');
        $hasSlug = $hasTable ? Schema::hasColumn('pages', 'slug') : false;
        $hasRoute = $hasTable ? Schema::hasColumn('pages', 'route') : false;
        $hasFeatureImage = $hasTable ? Schema::hasColumn('pages', 'feature_image') : false;
        $hasPageFeatureImage = $hasTable ? Schema::hasColumn('pages', 'page_feature_image') : false;

        // Posts table checks
        $postsTable = config('sabhero-articles.tables.prefix').'posts';
        $hasPostsTable = Schema::hasTable($postsTable);
        $hasOldAltText = $hasPostsTable ? Schema::hasColumn($postsTable, 'feature_image_alt_text') : false;
        $hasNewAltText = $hasPostsTable ? Schema::hasColumn($postsTable, 'post_feature_image_alt_text') : false;

        // Media collection checks
        $hasMediaTable = Schema::hasTable('media');
        $hasOldMediaCollections = false;
        if ($hasMediaTable) {
            $hasOldMediaCollections = DB::table('media')
                ->whereIn('collection_name', ['feature_image'])
                ->whereIn('model_type', [
                    'Fuelviews\\SabHeroArticles\\Models\\Page',
                    'Fuelviews\\SabHeroArticles\\Models\\Post'
                ])
                ->exists();
        }

        $this->comment("  → pages table exists: " . ($hasTable ? 'YES' : 'NO'));
        $this->comment("  → pages.slug column exists: " . ($hasSlug ? 'YES' : 'NO'));
        $this->comment("  → pages.route column exists: " . ($hasRoute ? 'YES' : 'NO'));
        $this->comment("  → pages.feature_image column exists: " . ($hasFeatureImage ? 'YES' : 'NO'));
        $this->comment("  → pages.page_feature_image column exists: " . ($hasPageFeatureImage ? 'YES' : 'NO'));
        $this->comment("  → posts.feature_image_alt_text column exists: " . ($hasOldAltText ? 'YES' : 'NO'));
        $this->comment("  → posts.post_feature_image_alt_text column exists: " . ($hasNewAltText ? 'YES' : 'NO'));
        $this->comment("  → old media collections (feature_image) exist: " . ($hasOldMediaCollections ? 'YES' : 'NO'));
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

        if ($hasFeatureImage || $hasPageFeatureImage) {
            $upgrades[] = 'Drop feature_image/page_feature_image columns (handled by media library)';
            $databaseNeedsUpgrade = true;
        }

        if ($hasOldAltText && !$hasNewAltText) {
            $upgrades[] = 'Rename posts.feature_image_alt_text to posts.post_feature_image_alt_text';
            $databaseNeedsUpgrade = true;
        }

        if ($hasOldMediaCollections) {
            $upgrades[] = 'Update media collection names (feature_image → post/page_feature_image)';
            $databaseNeedsUpgrade = true;
        }

        // Check if published files need updating (even if database is OK)
        $this->newLine();
        $this->info('Checking published files...');

        $publishedSeeder = $this->findPublishedPageSeeder();

        $seederNeedsUpdate = false;

        if ($publishedSeeder) {
            $seederContent = File::get($publishedSeeder);
            $seederNeedsUpdate = str_contains($seederContent, "'slug'") ||
                                 str_contains($seederContent, '"slug"') ||
                                 str_contains($seederContent, '$pageBySlug');

            // Force update if --force flag is set
            if ($this->option('force') && !$seederNeedsUpdate) {
                $seederNeedsUpdate = true;
                $this->comment("  → Seeder needs update: YES (forced)");
            } else {
                $this->comment("  → Seeder needs update: " . ($seederNeedsUpdate ? 'YES' : 'NO'));
            }

            if ($seederNeedsUpdate && !$this->option('force')) {
                // Show what will be replaced
                $this->comment("    - Found 'slug' references in seeder");
            }
        }

        if ($seederNeedsUpdate) {
            $upgrades[] = 'Update published PageTableSeeder';
        }

        // Check if wrapper package needs upgrade
        $this->newLine();
        $this->info('Checking wrapper package...');
        $wrapperNeedsUpgrade = false;

        $composerLockPath = base_path('composer.lock');
        if (File::exists($composerLockPath)) {
            $composerLock = json_decode(File::get($composerLockPath), true);
            $wrapperPackage = collect($composerLock['packages'] ?? [])
                ->firstWhere('name', 'fuelviews/laravel-sabhero-wrapper');

            if ($wrapperPackage) {
                $currentVersion = $wrapperPackage['version'] ?? 'unknown';
                $this->comment("  → Current version: {$currentVersion}");

                // Check if version is less than 2.0
                if (version_compare(ltrim($currentVersion, 'v'), '2.0', '<')) {
                    $upgrades[] = 'Upgrade wrapper package to ^2.0';
                    $wrapperNeedsUpgrade = true;
                    $this->comment("  → Needs upgrade: YES");
                } else {
                    $this->comment("  → Needs upgrade: NO (already >= 2.0)");
                }
            } else {
                $this->comment("  → Wrapper package not installed (skipping)");
            }
        } else {
            $this->comment("  → composer.lock not found (skipping check)");
        }

        if (empty($upgrades)) {
            $this->newLine();
            $this->info('✓ Your installation is already up to date!');
            return self::SUCCESS;
        }

        // Show what will be upgraded
        $this->newLine();
        $this->warn('  The following upgrades will be performed:');
        foreach ($upgrades as $upgrade) {
            $this->line("  • {$upgrade}");
        }
        $this->newLine();

        if (! $this->option('force') && ! $this->confirm('Do you want to continue?', true)) {
            $this->error('Upgrade cancelled.');

            return self::FAILURE;
        }

        // Only run database migration if needed
        if (!$databaseNeedsUpgrade) {
            $this->info('Skipping database migration (already upgraded)');
            goto updateFiles;
        }

        // Publish migrations to app if not already there
        $this->info('Publishing upgrade migrations...');

        $appMigrationsPath = database_path('migrations');

        // Publish rename_page_slug_to_route migration if slug column exists
        if ($hasSlug && !$hasRoute) {
            $packageMigrationFile = __DIR__ . '/../../database/migrations/rename_page_slug_to_route.php';
            $this->comment("  → Package migration: {$packageMigrationFile}");

            if (!File::exists($packageMigrationFile)) {
                $this->error("  ✗ rename_page_slug_to_route.php not found!");
                return self::FAILURE;
            }

            $timestamp = date('Y_m_d_His');
            $appMigrationFile = $appMigrationsPath . '/' . $timestamp . '_rename_page_slug_to_route.php';

            $existingMigrations = File::glob($appMigrationsPath . '/*_rename_page_slug_to_route.php');

            if (!empty($existingMigrations)) {
                $this->warn("  ⚠ rename_page_slug_to_route already published: " . basename($existingMigrations[0]));
            } else {
                File::copy($packageMigrationFile, $appMigrationFile);
                $this->line("  ✓ Published: " . basename($appMigrationFile));
            }
        }

        // Publish rename_feature_image_alt_text_column migration if old alt text column exists
        if ($hasOldAltText && !$hasNewAltText) {
            $packageMigrationFile = __DIR__ . '/../../database/migrations/rename_feature_image_alt_text_column.php';
            $this->comment("  → Package migration: {$packageMigrationFile}");

            if (!File::exists($packageMigrationFile)) {
                $this->error("  ✗ rename_feature_image_alt_text_column.php not found!");
                return self::FAILURE;
            }

            $timestamp = date('Y_m_d_His', time() + 1); // Add 1 second to ensure different timestamp
            $appMigrationFile = $appMigrationsPath . '/' . $timestamp . '_rename_feature_image_alt_text_column.php';

            $existingMigrations = File::glob($appMigrationsPath . '/*_rename_feature_image_alt_text_column.php');

            if (!empty($existingMigrations)) {
                $this->warn("  ⚠ rename_feature_image_alt_text_column already published: " . basename($existingMigrations[0]));
            } else {
                File::copy($packageMigrationFile, $appMigrationFile);
                $this->line("  ✓ Published: " . basename($appMigrationFile));
            }
        }

        // Publish drop_feature_image_columns migration if either column exists
        if ($hasFeatureImage || $hasPageFeatureImage) {
            $packageMigrationFile = __DIR__ . '/../../database/migrations/drop_feature_image_columns.php';
            $this->comment("  → Package migration: {$packageMigrationFile}");

            if (!File::exists($packageMigrationFile)) {
                $this->error("  ✗ drop_feature_image_columns.php not found!");
                return self::FAILURE;
            }

            $timestamp = date('Y_m_d_His', time() + 2); // Add 2 seconds to ensure different timestamp
            $appMigrationFile = $appMigrationsPath . '/' . $timestamp . '_drop_feature_image_columns.php';

            $existingMigrations = File::glob($appMigrationsPath . '/*_drop_feature_image_columns.php');

            if (!empty($existingMigrations)) {
                $this->warn("  ⚠ drop_feature_image_columns already published: " . basename($existingMigrations[0]));
            } else {
                File::copy($packageMigrationFile, $appMigrationFile);
                $this->line("  ✓ Published: " . basename($appMigrationFile));
            }
        }

        // Publish rename_media_collection_names migration if old media collections exist
        if ($hasOldMediaCollections) {
            $packageMigrationFile = __DIR__ . '/../../database/migrations/rename_media_collection_names.php';
            $this->comment("  → Package migration: {$packageMigrationFile}");

            if (!File::exists($packageMigrationFile)) {
                $this->error("  ✗ rename_media_collection_names.php not found!");
                return self::FAILURE;
            }

            $timestamp = date('Y_m_d_His', time() + 3); // Add 3 seconds to ensure different timestamp
            $appMigrationFile = $appMigrationsPath . '/' . $timestamp . '_rename_media_collection_names.php';

            $existingMigrations = File::glob($appMigrationsPath . '/*_rename_media_collection_names.php');

            if (!empty($existingMigrations)) {
                $this->warn("  ⚠ rename_media_collection_names already published: " . basename($existingMigrations[0]));
            } else {
                File::copy($packageMigrationFile, $appMigrationFile);
                $this->line("  ✓ Published: " . basename($appMigrationFile));
            }
        }

        $this->comment("  → App migrations: {$appMigrationsPath}");

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
        $this->newLine();
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

        // Upgrade wrapper package if needed
        if ($wrapperNeedsUpgrade) {
            $this->newLine();
            $this->info('Upgrading wrapper package...');
            $this->comment('  → Running: composer require fuelviews/laravel-sabhero-wrapper:^2.0 --no-interaction');
            $this->newLine();

            $exitCode = $this->runComposerCommand('require fuelviews/laravel-sabhero-wrapper:^2.0 --no-interaction');

            $this->newLine();
            $this->comment("  → Composer exit code: {$exitCode}");

            if ($exitCode !== 0) {
                $this->error('Wrapper package upgrade failed with exit code: ' . $exitCode);
                $this->warn('You may need to manually run: composer require fuelviews/laravel-sabhero-wrapper:^2.0');
            } else {
                $this->line('  ✓ Wrapper package upgraded successfully');
            }
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

        // Replace $pageData['slug'] with $pageData['route']
        $content = str_replace("\$pageData['slug']", "\$pageData['route']", $content);

        // Replace ->where('slug', with ->where('route', (instance method)
        $content = str_replace("->where('slug',", "->where('route',", $content);

        // Replace ::where('slug', with ::where('route', (static method)
        $content = str_replace("::where('slug',", "::where('route',", $content);

        // Replace variable names $pageBySlug with $pageByRoute
        $content = str_replace('$pageBySlug', '$pageByRoute', $content);
        $content = str_replace('$pageByRoute', '$pageByRoute', $content);

        // Replace comments about slug with route
        $content = str_replace('slug OR title', 'route OR title', $content);
        $content = str_replace('finding by slug', 'finding by route', $content);
        $content = str_replace('neither slug nor title', 'neither route nor title', $content);
        $content = str_replace('actually needs the route name, not the slug', 'Laravel route name for this page', $content);

        File::put($filePath, $content);
    }

    /**
     * Run a composer command and return the exit code.
     */
    protected function runComposerCommand(string $command): int
    {
        $composerPath = base_path();

        // Try to find composer binary
        $composerBinary = 'composer';

        // Check if composer.phar exists
        if (file_exists($composerPath . '/composer.phar')) {
            $composerBinary = 'php composer.phar';
        }

        // Build full command
        $fullCommand = "cd {$composerPath} && {$composerBinary} {$command}";

        // Execute and capture output
        exec($fullCommand . ' 2>&1', $output, $exitCode);

        // Display output
        foreach ($output as $line) {
            $this->line($line);
        }

        return $exitCode;
    }
}
