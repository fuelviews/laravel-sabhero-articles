<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Breadcrumbs::for('home', static function ($trail) {
    $homeRoute = route(Route::has('home') ? 'home' : 'welcome');
    $trail->push('', $homeRoute, [
        'icon' => '<svg fill="currentColor" viewBox="0 0 576 512" class="h-5 w-5">
                <path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/>
            </svg>',
    ]);
});

Breadcrumbs::for('sabhero-article.post.index', static function (BreadcrumbTrail $trail) {
    $prefix = config('sabhero-article.route.prefix');

    $trail->parent('home');
    $trail->push(Str::title($prefix), route('sabhero-article.post.index'));
});

Breadcrumbs::for('sabhero-article.post.show', static function (BreadcrumbTrail $trail, $post) {
    $trail->parent('sabhero-article.post.index');
    $trail->push($post->title, route('sabhero-article.post.show', $post->slug));
});

Breadcrumbs::for('sabhero-article.category.index', static function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Categories', route('sabhero-article.category.index'));
});

Breadcrumbs::for('sabhero-article.category.post', static function (BreadcrumbTrail $trail, $category) {
    $trail->parent('sabhero-article.category.index');
    $trail->push($category->name, route('sabhero-article.category.post', $category->slug));
});

Breadcrumbs::for('sabhero-article.tag.post', static function (BreadcrumbTrail $trail, $tag) {
    $trail->parent('sabhero-article.tag.index');
    $trail->push($tag->name, route('sabhero-article.tag.post', $tag->slug));
});

Breadcrumbs::for('sabhero-article.tag.index', static function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Tags', route('sabhero-article.tag.index'));
});

Breadcrumbs::for('sabhero-article.author.index', static function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Authors', route('sabhero-article.author.index'));
});

Breadcrumbs::for('sabhero-article.author.show', static function (BreadcrumbTrail $trail, $author) {
    $trail->parent('sabhero-article.author.index');
    $trail->push($author->name, route('sabhero-article.author.show', $author->slug));
});
