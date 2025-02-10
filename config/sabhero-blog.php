<?php

/**
 * |--------------------------------------------------------------------------
 * | Set up your blog configuration
 * |--------------------------------------------------------------------------
 * |
 * | The route configuration is for setting up the route prefix and middleware.
 * | The user configuration is for setting up the user model and columns.
 * | The seo configuration is for setting up the default meta tags for the blog.
 * | The recaptcha configuration is for setting up the recaptcha for the blog.
 */

use Fuelviews\SabHeroBlog\Models\User;

return [
    'tables' => [
        'prefix' => 'blog_', // prefix for all blog tables
    ],
    'route' => [
        'prefix' => 'blog',
        'middleware' => ['web'],
        'login' => [
            'name' => 'filament.admin.auth.login',
        ],
    ],
    'dropdown' => [
        'name' => 'Posts',
    ],
    'user' => [
        'model' => User::class,
        'foreign_key' => 'user_id',
        'columns' => [
            'name' => 'name',
            'avatar' => 'profile_photo_path',
            'slug' => 'slug'
        ],
    ],
    'heading_permalink' => [
        'html_class' => 'scroll-mt-40',
    ],
];
