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
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, TwoFactorAuthenticatable, HasBlog;

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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'links' => 'array',
            'is_author' => 'boolean'
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
        $this->addMediaConversion('author')
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
