# SAB Hero Articles Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fuelviews/laravel-sabhero-articles.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sabhero-articles)
[![Total Downloads](https://img.shields.io/packagist/dt/fuelviews/laravel-sabhero-articles.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sabhero-articles)

A full-featured article management solution for Laravel applications with Filament admin panel integration. This package provides a complete article publishing platform with advanced features and an intuitive admin interface.

## Features

- **Complete Article Management**: Posts, categories, tags, and authors
- **Scheduled Publishing**: Schedule posts to be published automatically
- **Blade Components**: Ready-to-use UI components including cards, feature cards, and breadcrumbs
- **Advanced Content**: Markdown rendering with automatic table of contents
- **Media Management**: Image uploads with responsive images support
- **SEO Optimization**: Built-in SEO metadata for better search rankings
- **RSS Feed**: Automatic feed generation with customizable settings
- **Tailwind Pagination**: Custom pagination views for Tailwind CSS
- **Filament Integration**: Full admin panel for managing all article content

## Installation

### Prerequisites

This package requires Filament. If your project doesn't have Filament yet:

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### Create a Filament User
```bash
php artisan make:filament-user
```

### Install the SAB Hero Articles Package

```bash
composer require fuelviews/laravel-sabhero-articles
```

## Configuration

### 1. Publish Configuration Files

```bash
php artisan vendor:publish --tag="sabhero-articles-config"
```

### 2. Publish Migrations

```bash
php artisan vendor:publish --tag="sabhero-articles-migrations"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Publish Seeders

```bash
php artisan vendor:publish --tag="sabhero-articles-seeders"
```

### 5. Publish Seeders

Fill pages data in `database/seeders/PageTableSeeder.php`

### 6. Run Seeders

```bash
php artisan db:seed --class=PageTableSeede
```


## Integration

### 1. Attach to Filament Panel

Add the SAB Hero Articles plugin to your Filament panel provider:

```php
use Fuelviews\SabHeroArticles\Facades\SabHeroArticles;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            SabHeroArticles::make()
        ]);
}
```

### 2. Add Traits and CanAccessPanel to User Model

Your user model needs to be setup to use the `HasArticle` trait and ensure the required fields are in the fillable array:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Fuelviews\SabHeroArticles\Traits\HasArticle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasArticle;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'bio',
        'links',
        'is_author',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'links' => 'array',
        'is_author' => 'boolean',
    ];
    
    public function canAccessPanel(Panel $panel): bool
    {
        $allowedDomains = config('sabhero-articles.user.allowed_domains', []);

        foreach ($allowedDomains as $domain) {
            if (str_ends_with($this->email, $domain)) {
                return true;
            }
        }

        return false;
    }
}
```

## Available Components

SAB Hero Articles comes with several Blade components for easy UI implementation:

- `<x-sabhero-articles::layout>` - Main article layout
- `<x-sabhero-articles::card>` - Article post-card
- `<x-sabhero-articles::feature-card>` - Featured post-card
- `<x-sabhero-articles::breadcrumb>` - Breadcrumb navigation
- `<x-sabhero-articles::header-category>` - Category header
- `<x-sabhero-articles::header-metro>` - Metro-style header
- `<x-sabhero-articles::markdown>` - Markdown content renderer
- `<x-sabhero-articles::recent-post>` - Recent posts display

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for contribution guidelines.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Joshua Mitchener](https://github.com/thejmitchener)
- [Daniel Clark](https://github.com/sweatybreeze)
- [Fuelviews](https://github.com/fuelviews)
- [Firefly](https://github.com/thefireflytech)
- [Asmit Nepali](https://github.com/AsmitNepali)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
