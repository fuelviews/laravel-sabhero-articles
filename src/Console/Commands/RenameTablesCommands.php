<?php

namespace Fuelviews\SabHeroBlog\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RenameTablesCommands extends Command
{
    protected $signature = 'sabhero-blog:upgrade-tables';

    protected $description = 'This command helps to rename the tables. The tables name are now configurable in sabhero-blog config file.';
    public function handle()
    {
        Artisan::call('migrate', [
            '--path' => 'vendor/fuelviews/sabhero-blog/database/migrations/2024_05_11_152936_create_add_prefix_on_all_blog_tables.php',
            '--force' => true,
        ]);
        $this->info('Tables have been renamed successfully.');
    }
}
