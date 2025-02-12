<?php

namespace Fuelviews\SabHeroBlog\Models;

use Fuelviews\SabHeroBlog\Traits\HasBlog;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserBak extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia, HasBlog;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'bio',
        'links',
        'is_author'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'links' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@admin.com') /*&& $this->hasVerifiedEmail()*/;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->withResponsiveImages()
            ->width(208)
            ->height(208)
            ->fit(Fit::Crop, 208, 208)
            ->performOnCollections('avatar');
    }

    public function getNameAttribute($value): string
    {
        return Str::title($value);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?:  $this->avatar_url;
    }
}
