<?php

namespace Fuelviews\SabHeroBlog\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Fuelviews\SabHeroBlog\Traits\HasBlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasBlog;
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'bio',
        'links',
        'is_author',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'links' => 'array',
        'is_author' => 'boolean',
    ];

    /**
     * Boot the model - auto-generate slugs
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get all posts by this author
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-blog.user.foreign_key'));
    }

    /**
     * Register media collections for blog functionality
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->withResponsiveImages();
    }

    /**
     * Register media conversions for blog functionality
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->withResponsiveImages()
            ->width(208)
            ->height(208)
            ->fit(Fit::Crop, 208, 208)
            ->performOnCollections('avatar');
    }

    /**
     * Get responsive image srcset for author avatar
     */
    public function getAuthorMediaSrcSet(): ?string
    {
        $media = $this->getFirstMedia('avatar');

        return $media ? $media->getSrcset() : null;
    }

    /**
     * Get author avatar URL with intelligent fallback
     */
    public function getAuthorAvatarUrl(): string
    {
        // Try media library first
        $media = $this->getFirstMedia('avatar');
        if ($media && $media->getUrl()) {
            return $media->getUrl();
        }

        // Try avatar_url attribute
        if (! empty($this->avatar_url)) {
            return $this->avatar_url;
        }

        // Fallback to generated avatar
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF&rounded=true';
    }

    /**
     * Legacy avatar attribute (maintains backward compatibility)
     */
    public function getAvatarAttribute(): string
    {
        return $this->getAuthorAvatarUrl();
    }

    /**
     * Get Filament avatar URL (required by HasAvatar interface)
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: $this->avatar_url ?? null;
    }

    /**
     * Use slug for route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the author's display name
     */
    public function name(): string
    {
        return $this->{config('sabhero-blog.user.columns.name', 'name')};
    }

    /**
     * Capitalize the first letter of the name
     */
    public function getNameAttribute($value): string
    {
        return ucfirst($value);
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
