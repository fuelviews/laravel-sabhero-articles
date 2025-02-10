<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

use Fuelviews\SabHeroBlog\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function posts(User $author)
    {
        $posts = $author->posts()
            ->with(['categories', 'user', 'tags', 'state', 'city'])
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.show', [
            'posts' => $posts,
            'author' => $author,
        ]);
    }

    public function allAuthors()
    {
        $authors = User::where('is_author', true)
            ->withCount([
                'posts' => function ($query) {
                    $query->where('status', 'published', true);
                },
            ])
            ->orderBy('name')
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.index', [
            'authors' => $authors,
        ]);
    }
}
