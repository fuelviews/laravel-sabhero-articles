<?php

use Fuelviews\SabHeroArticles\Models\User;

return [
    'tables' => [
        'prefix' => 'articles_',
    ],

    'route' => [
        'prefix' => 'articles',
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

    'media' => [
        'disk' => 'public',
    ],
];
