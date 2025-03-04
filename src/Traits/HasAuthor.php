<?php

namespace Fuelviews\SabHeroBlog\Traits;

use Fuelviews\SabHeroBlog\Models\Author;
use Illuminate\Support\Str;

trait HasAuthor
{
    public function author()
    {
        return $this->hasOne(Author::class);
    }

    public function initializeHasAuthor()
    {
        $this->with[] = 'author';
    }

    public static function bootHasAuthor()
    {
        static::created(function ($user) {
            $user->author()->create([
                'user_id' => $user->id,
                'slug' => Str::slug($user->name),
                'is_author' => false,
                'links' => [],
                'bio' => null,
            ]);
        });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->author?->getFirstMediaUrl('avatar') ?: $this->avatar_url ?? null;
    }
}
