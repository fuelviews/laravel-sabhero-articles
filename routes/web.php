<?php

use Fuelviews\SabHeroBlog\Http\Controllers\AuthorController;
use Fuelviews\SabHeroBlog\Http\Controllers\CategoryController;
use Fuelviews\SabHeroBlog\Http\Controllers\PostController;
use Fuelviews\SabHeroBlog\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('sabhero-blog.route.middleware'))
    ->prefix(config('sabhero-blog.route.prefix'))
    ->group(function () {
        Route::get('/', [PostController::class, 'index'])
            ->name('sabhero-blog.post.index');
        Route::get('/search', [PostController::class, 'search'])
            ->name('sabhero-blog.post.search');
        Route::get('/tags', [TagController::class, 'allTags'])
            ->name('sabhero-blog.tag.index');
        Route::get('/categories', [CategoryController::class, 'allCategories'])
            ->name('sabhero-blog.category.index');
        Route::get('/authors', [AuthorController::class, 'allAuthors'])
            ->name('sabhero-blog.author.index');
        Route::get('/authors/{author:slug}', [AuthorController::class, 'posts'])
            ->name('sabhero-blog.author.show');
        Route::feeds();
        Route::get('/{post:slug}', [PostController::class, 'show'])
            ->name('sabhero-blog.post.show');
        Route::get('/categories/{category:slug}', [CategoryController::class, 'posts'])
            ->name('sabhero-blog.category.post');
        Route::get('/tags/{tag:slug}', [TagController::class, 'posts'])
            ->name('sabhero-blog.tag.post');
    });

/*Route::middleware(config('sabhero-blog.route.middleware'))
    ->prefix('locations')
    ->group(function () {
        Route::get('/{state:slug}', [PostController::class, 'indexMetroState'])
            ->name('sabhero-blog.post.metro.state.index');
        Route::get('/{state:slug}/{city:slug}', [PostController::class, 'indexMetroStateCity'])
            ->name('sabhero-blog.post.metro.state.city.index');
        Route::get('/{state:slug}/{city:slug}/{post:slug}', [PostController::class, 'showMetro'])
            ->name('sabhero-blog.post.metro.show');
    });*/
