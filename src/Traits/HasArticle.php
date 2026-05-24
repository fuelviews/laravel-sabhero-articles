<?php

namespace Fuelviews\SabHeroArticles\Traits;

use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

trait HasArticle
{
    /**
     * Boot the trait - auto-generate slugs and set author status
     */
    protected static function bootHasArticle()
    {
        static::saving(function ($model) {
            // Only process slug if the column exists in fillable
            if (in_array('slug', $model->getFillable(), true)) {
                // Generate slug if empty OR if not properly slugified
                if (empty($model->slug) || $model->slug !== Str::slug($model->slug)) {
                    $model->slug = $model->generateUniqueSlug($model->name);
                }
            }

            // Set new users as authors by default (if column exists and not set)
            if (in_array('is_author', $model->getFillable(), true) && ! isset($model->is_author)) {
                $model->is_author = true;
            }
        });
    }

    /**
     * Generate a unique slug from the given name
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-articles.user.foreign_key'));
    }

    /**
     * Use slug for route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if this user is marked as a public author
     */
    public function isAuthor(): bool
    {
        return (bool) $this->is_author;
    }

    /**
     * Scope to get only public authors
     */
    public function scopeAuthors($query)
    {
        return $query->where('is_author', true);
    }

    /**
     * Scope to get authors with published posts
     */
    public function scopeActiveAuthors($query)
    {
        return $query->authors()
            ->whereHas('posts', function ($q) {
                $q->where('status', 'published');
            });
    }
}
