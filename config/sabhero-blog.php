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

use App\Models\User;

return [
    'tables' => [
        'prefix' => 'blog_', // prefix for all blog tables
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
