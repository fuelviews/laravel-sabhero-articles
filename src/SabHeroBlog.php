<?php

namespace Fuelviews\SabHeroBlog;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Fuelviews\SabHeroBlog\Filament\Components\AuthorProfile;
use Fuelviews\SabHeroBlog\Filament\Pages\HealthCheckResults;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class SabHeroBlog implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'sabhero-blog';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Filament\Resources\CategoryResource::class,
            Filament\Resources\PostResource::class,
            Filament\Resources\TagResource::class,
            Filament\Resources\MetroResource::class,
            Filament\Resources\PageResource::class,
            Filament\Resources\UserResource::class,
            Filament\Resources\RoleResource::class,
        ]);

        $panel->colors([
            'cyan' => Color::Cyan,
        ]);

        $panel->plugins([
            BreezyCore::make()
                ->myProfileComponents([
                    AuthorProfile::class,
                ])
                ->avatarUploadComponent(function () {
                    return SpatieMediaLibraryFileUpload::make('avatar')
                        ->disk('public')
                        ->collection('avatar')
                        ->visibility('public')
                        ->afterStateUpdated(function ($state) {
                            $user = auth()->user();
                            if (!$user) {
                                throw new RuntimeException('No user found to attach media.');
                            }

                            if ($state) {
                                $user->addMedia($state->getRealPath())
                                    ->usingFileName($state->getClientOriginalName())
                                    ->toMediaCollection('avatar');
                            }
                        });
                })
                ->enableSanctumTokens(
                    permissions: ['null']
                )
                ->myProfile(
                    hasAvatars: true,
                ),
        ]);

        $panel->navigationItems([
            NavigationItem::make('Fuelviews CRM')
                ->url('https://app.gohighlevel.com', shouldOpenInNewTab: true)
                ->icon('heroicon-o-presentation-chart-line')
                ->sort(1),
        ]);

        $panel->sidebarCollapsibleOnDesktop();
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
