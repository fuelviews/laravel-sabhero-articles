<?php

namespace Fuelviews\SabHeroBlog\Traits;

use Fuelviews\SabHeroBlog\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBlog
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-blog.user.foreign_key'));
    }
}