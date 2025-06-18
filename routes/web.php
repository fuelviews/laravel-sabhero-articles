<?php

use Fuelviews\SabHeroArticle\Http\Controllers\AuthorController;
use Fuelviews\SabHeroArticle\Http\Controllers\CategoryController;
use Fuelviews\SabHeroArticle\Http\Controllers\PostController;
use Fuelviews\SabHeroArticle\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('sabhero-article.route.middleware'))
    ->prefix(config('sabhero-article.route.prefix'))
    ->group(function () {
        Route::get('/', [PostController::class, 'index'])
            ->name('sabhero-article.post.index');
        Route::get('/page/{page}', [PostController::class, 'index'])
            ->where('page', '[0-9]+')
            ->name('sabhero-article.post.page');
        Route::get('/search', [PostController::class, 'search'])
            ->name('sabhero-article.post.search');
        Route::get('/tags', [TagController::class, 'allTags'])
            ->name('sabhero-article.tag.index');
        Route::get('/categories', [CategoryController::class, 'allCategories'])
            ->name('sabhero-article.category.index');
        Route::get('/authors', [AuthorController::class, 'allAuthors'])
            ->name('sabhero-article.author.index');
        Route::get('/authors/{user:slug}', [AuthorController::class, 'posts'])
            ->name('sabhero-article.author.show');
        Route::feeds();
        Route::get('/{post:slug}', [PostController::class, 'show'])
            ->name('sabhero-article.post.show');
        Route::get('/categories/{category:slug}', [CategoryController::class, 'posts'])
            ->name('sabhero-article.category.post');
        Route::get('/tags/{tag:slug}', [TagController::class, 'posts'])
            ->name('sabhero-article.tag.post');
    });
