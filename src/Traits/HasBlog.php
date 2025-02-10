<?php

namespace Fuelviews\SabHeroBlog\Traits;

use Fuelviews\SabHeroBlog\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBlog
{
    public function name(): string
    {
        return $this->{config('sabhero-blog.user.columns.name')};
    }

    public function getAvatarAttribute(): string
    {
        if ($this->hasMedia('avatar')) {
            return $this->getFirstMediaUrl('avatar');
        }

        if (!empty($this->{config('sabhero-blog.user.columns.avatar')})) {
            return asset('storage/' . $this->{config('sabhero-blog.user.columns.avatar')});
        }

        return 'https://ui-avatars.com/api/?background=random&name=' . urlencode($this->{config('sabhero-blog.user.columns.name')});
    }
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-blog.user.foreign_key'));
    }
}
