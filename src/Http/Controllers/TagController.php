<?php

namespace Fuelviews\SabHeroArticle\Http\Controllers;

use Fuelviews\SabHeroArticle\Models\Post;
use Fuelviews\SabHeroArticle\Models\Tag;

class TagController extends Controller
{
    public function posts(Tag $tag)
    {
        $posts = $tag->load(['posts.user'])
            ->posts()
            ->published()
            ->paginate(10);

        return view('sabhero-article::articles.tags.show', [
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

        return view('sabhero-article::articles.tags.index', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }
}
