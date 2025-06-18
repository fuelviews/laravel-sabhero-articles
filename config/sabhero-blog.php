<?php

use Fuelviews\SabHeroBlog\Models\User;

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
        'allowed_domains' => [
            '@fuelviews.com',
        ],
    ],
    'heading_permalink' => [
        'html_class' => 'scroll-mt-40',
    ],
    'crm' => [
        'name' => 'CRM',
        'link' => '#',
    ],
];
