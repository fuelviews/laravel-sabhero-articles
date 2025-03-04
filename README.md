# SabHero Blog Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fuelviews/laravel-sabhero-blog.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sabhero-blog)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-sabhero-blog/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/fuelviews/laravel-sabhero-blog/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fuelviews/laravel-sabhero-blog/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/fuelviews/laravel-sabhero-blog/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fuelviews/laravel-sabhero-blog.svg?style=flat-square)](https://packagist.org/packages/fuelviews/laravel-sabhero-blog)

## Installation
If your project is not already using Filament, you can install it by running the following commands:
```bash
composer require filament/filament:"^3.2" -W
```
```bash
php artisan filament:install --panels
```
Install the SabHero Blog Plugin by running the following command:
 ```bash
composer require fuelviews/sabhero-blog -W
```

## Usage
After composer require, you can start using the SabHero Blog Plugin by running the following command:

```bash
php artisan sabhero-blog:install
```
This command will publish `sabhero-blog.php` config file and migration files.
````php
<?php

use App\Models\User;

return [
    'tables' => [
        'prefix' => 'blog_',
    ],
    'route' => [
        'prefix' => 'blog',
        'middleware' => ['web'],
    ],
    'user' => [
        'model' => User::class,
        'foreign_key' => 'user_id',
        'columns' => [
            'name' => 'name',
            'slug' => 'slug',
        ],
    ],
    'heading_permalink' => [
        'html_class' => 'scroll-mt-40',
    ],
    'dropdown' => [
        'name' => 'Posts',
    ],
    'crm' => [
        'name' => 'CRM',
        'link' => '#',
    ],
];

````
Before running the migration, you can modify the `sabhero-blog.php` config file to suit your needs.

You can publish the config file with:

```bash
php artisan vendor:publish --tag="sabhero-blog-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="sabhero-blog-views"
```

## Migrate the database
After modifying the `sabhero-blog.php` config file, you can run the migration by running the following command:

```bash
php artisan migrate
```

## Attach SabHero Blog panel to the dashboard
You can attach the SabHero Blog panel to the dashboard by adding the following code to your panel provider:
Add `SabHeroBlog::make()` to your panel passing the class to your `plugins()` method.

```php
use Fuelviews\SabHeroBlog\SabHeroBlog;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            SabHeroBlog::make()
        ])
}
```

## Authorizing access to the panel
By default, all App\Models\Users can access Filament locally. To allow them to access Filament in production, you must take a few extra steps to ensure that only the correct users have access to the app.

```php
<?php

namespace App\Models;

use Filament\Panel;
use Fuelviews\SabHeroBlog\Traits\HasAuthor;
use Fuelviews\SabHeroBlog\Traits\HasBlog;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasBlog, HasAuthor;
    
    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@your-domain-here.com');
    }
}
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="sabhero-blog-migrations"
php artisan migrate
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Thejmitchener](https://github.com/thejmitchener)
- [Sweatybreeze](https://github.com/sweatybreeze)
- [Fuelviews](https://github.com/fuelviews)
- [Firefly](https://github.com/thefireflytech)
- [Asmit Nepali](https://github.com/AsmitNepali)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
