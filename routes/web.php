<?php

use Fuelviews\SabHeroArticles\Http\Controllers\AuthorController;
use Fuelviews\SabHeroArticles\Http\Controllers\CategoryController;
use Fuelviews\SabHeroArticles\Http\Controllers\PostController;
use Fuelviews\SabHeroArticles\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('sabhero-articles.route.middleware'))
    ->prefix(config('sabhero-articles.route.prefix'))
    ->group(function () {
        Route::get('/', [PostController::class, 'index'])
            ->name('sabhero-articles.post.index');
        Route::get('/page/{page}', [PostController::class, 'index'])
            ->where('page', '[0-9]+')
            ->name('sabhero-articles.post.page');
        Route::get('/search', [PostController::class, 'search'])
            ->name('sabhero-articles.post.search');
        Route::get('/tags', [TagController::class, 'allTags'])
            ->name('sabhero-articles.tag.index');
        Route::get('/categories', [CategoryController::class, 'allCategories'])
            ->name('sabhero-articles.category.index');
        Route::get('/authors', [AuthorController::class, 'allAuthors'])
            ->name('sabhero-articles.author.index');
        Route::get('/authors/{user:slug}', [AuthorController::class, 'posts'])
            ->name('sabhero-articles.author.show');
        Route::feeds();
        Route::get('/{post:slug}', [PostController::class, 'show'])
            ->name('sabhero-articles.post.show');
        Route::get('/categories/{category:slug}', [CategoryController::class, 'posts'])
            ->name('sabhero-articles.category.post');
        Route::get('/tags/{tag:slug}', [TagController::class, 'posts'])
            ->name('sabhero-articles.tag.post');
    });
