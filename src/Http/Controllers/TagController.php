<?php

namespace Fuelviews\SabHeroArticles\Http\Controllers;

use Fuelviews\SabHeroArticles\Models\Post;
use Fuelviews\SabHeroArticles\Models\Tag;

class TagController extends Controller
{
    public function posts(Tag $tag)
    {
        $posts = $tag->load(['posts.user'])
            ->posts()
            ->published()
            ->paginate(10);

        return view('sabhero-articles::articles.tags.show', [
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }

    public function allTags()
    {
        $posts = Post::published()
            ->with(['categories', 'user', 'tags'])
            ->latest()
            ->paginate(10);

        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('sabhero-articles::articles.tags.index', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }
}
