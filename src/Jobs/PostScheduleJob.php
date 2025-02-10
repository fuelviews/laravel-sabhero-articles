<?php

namespace Fuelviews\SabHeroBlog\Jobs;

use Fuelviews\SabHeroBlog\Enums\PostStatus;
use Fuelviews\SabHeroBlog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Post $post)
    {
        //
    }

    public function handle(): void
    {
        Log::info('PostScheduleJob Started');
        $this->post->update([
            'status' => PostStatus::PUBLISHED,
            'published_at' => now(),
            'scheduled_for' => null,
        ]);
        Log::info('PostScheduleJob Ended');
    }
}
