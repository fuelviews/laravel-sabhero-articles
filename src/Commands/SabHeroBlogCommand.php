<?php

namespace Fuelviews\SabHeroBlog\Commands;

use Illuminate\Console\Command;

class SabHeroBlogCommand extends Command
{
    public $signature = 'laravel-sabhero-blog';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
