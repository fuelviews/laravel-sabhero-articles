<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

use App\Models\User;
use Fuelviews\SabHeroBlog\Models\Author;

class AuthorController extends Controller
{
    public function posts(Author $author)
    {
        $posts = $author->user
            ->posts()
            ->with(['categories', 'user', 'tags'])
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.show', [
            'posts' => $posts,
            'author' => $author,
        ]);
    }

    public function allAuthors()
    {
        $authors = User::whereHas('author', function ($query) {
            $query->where('is_author', true);
        })
            ->withCount([
                'posts' => function ($query) {
                    $query->published();
                },
            ])
            ->orderBy('name') // or orderBy('your_column') as needed
            ->paginate(10);

        return view('sabhero-blog::blogs.authors.index', [
            'authors' => $authors,
        ]);
    }
}
