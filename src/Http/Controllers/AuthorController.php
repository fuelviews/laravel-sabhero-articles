<?php

namespace Fuelviews\SabHeroArticle\Http\Controllers;

class AuthorController extends Controller
{
    public function posts($user)
    {
        $posts = $user->posts()
            ->with(['categories', 'user', 'tags'])
            ->published()
            ->paginate(10);

        return view('sabhero-article::articles.authors.show', [
            'posts' => $posts,
            'author' => $user,
        ]);
    }

    public function allAuthors()
    {
        $userModel = config('sabhero-article.user.model');

        $authors = $userModel::activeAuthors()
            ->withCount([
                'posts' => function ($query) {
                    $query->published();
                },
            ])
            ->orderBy('name')
            ->paginate(10);

        return view('sabhero-article::articles.authors.index', [
            'authors' => $authors,
        ]);
    }
}
