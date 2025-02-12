<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PostResource\Pages;

use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Fuelviews\SabHeroBlog\Events\BlogPublished;
use Fuelviews\SabHeroBlog\Filament\Resources\PostResource;
use Fuelviews\SabHeroBlog\Jobs\PostScheduleJob;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->isScheduled()) {

            $now = Carbon::now();
            $scheduledFor = Carbon::parse($this->record->scheduled_for);
            PostScheduleJob::dispatch($this->record)
                ->delay($now->diffInSeconds($scheduledFor));
        }
        if ($this->record->isStatusPublished()) {
            $this->record->published_at = date('Y-m-d H:i:s');
            $this->record->save();
            event(new BlogPublished($this->record));
        }
    }
}
