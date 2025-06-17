<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

class AuthorController extends Controller
{
    public function posts($user)
    {
        $userModel = config('sabhero-blog.user.model');
        $user = $userModel::where('slug', $user)->firstOrFail();
        
        $posts = $user->posts()
            ->with(['categories', 'user', 'tags'])
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.show', [
            'posts' => $posts,
            'author' => $user,
        ]);
    }

    public function allAuthors()
    {
        $userModel = config('sabhero-blog.user.model');
        
        $authors = $userModel::activeAuthors()
            ->withCount([
                'posts' => function ($query) {
                    $query->published();
                },
            ])
            ->orderBy('name')
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.index', [
            'authors' => $authors,
        ]);
    }
}
