<?php

namespace Fuelviews\SabHeroArticle\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Fuelviews\SabHeroArticle\Traits\HasArticle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasArticle;
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

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
     * Get all posts by this author
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-article.user.foreign_key'));
    }

    /**
     * Register media collections for article functionality
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->withResponsiveImages();
    }

    /**
     * Register media conversions for article functionality
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
     * Get the author's display name
     */
    public function name(): string
    {
        return $this->{config('sabhero-article.user.columns.name', 'name')};
    }

    /**
     * Capitalize the first letter of the name
     */
    public function getNameAttribute($value): string
    {
        return ucfirst($value);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $allowedDomains = config('sabhero-article.user.allowed_domains', []);

        foreach ($allowedDomains as $domain) {
            if (str_ends_with($this->email, $domain)) {
                return true;
            }
        }

        return false;
    }
}
