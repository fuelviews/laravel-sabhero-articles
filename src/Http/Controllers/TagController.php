<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

use Fuelviews\SabHeroBlog\Models\Post;
use Fuelviews\SabHeroBlog\Models\Tag;

class TagController extends Controller
{
    public function posts(Tag $tag)
    {
        $posts = $tag->load(['posts.user'])
            ->posts()
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.tags.show', [
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }

    public function allTags()
    {
        $posts = Post::published()
            ->with(['categories', 'user', 'tags', 'state', 'city'])
            ->latest()
            ->paginate(10);

        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('sabhero-blog::blogs.tags.index', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }
}
