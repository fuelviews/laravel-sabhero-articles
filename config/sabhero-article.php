<?php

use Fuelviews\SabHeroArticle\Models\User;

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
    
    /*
    |--------------------------------------------------------------------------
    | Glide Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how images in markdown content are processed through Glide.
    | This allows for automatic optimization, resizing, and format conversion
    | of images referenced in markdown content.
    |
    */
    'glide' => [
        // Enable/disable Glide processing for markdown images
        'enabled' => env('SABHERO_GLIDE_ENABLED', true),
        
        // Base URL for Glide server (should match your Glide route)
        'base_url' => env('SABHERO_GLIDE_BASE_URL', '/img'),
        
        // Signature key for secure URLs (optional)
        'signature_key' => env('SABHERO_GLIDE_SIGNATURE_KEY', null),
        
        // Process external images through Glide proxy
        'process_external' => env('SABHERO_GLIDE_PROCESS_EXTERNAL', false),
        
        // Enable responsive images with srcset
        'responsive' => env('SABHERO_GLIDE_RESPONSIVE', true),
        
        // Enable lazy loading attribute
        'lazy_loading' => env('SABHERO_GLIDE_LAZY_LOADING', true),
        
        // Widths for responsive srcset generation
        'srcset_widths' => [400, 800, 1200, 1600, 2000],
        
        // Sizes attribute for responsive images
        'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 800px',
        
        // Default Glide parameters applied to all images
        'default_params' => [
            'q' => 80,              // Quality (1-100)
            'fm' => 'webp',         // Format (webp, jpg, png, etc.)
            'fit' => 'max',         // Fit mode (contain, max, fill, stretch, crop)
            'sharp' => 5,           // Sharpening (0-100)
        ],
    ],
];
