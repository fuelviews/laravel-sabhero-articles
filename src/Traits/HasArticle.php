<?php

namespace Fuelviews\SabHeroArticle\Traits;

use Fuelviews\SabHeroArticle\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasArticle
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-article.user.foreign_key'));
    }
}
