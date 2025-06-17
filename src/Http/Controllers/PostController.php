<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

use Fuelviews\SabHeroBlog\Models\Metro;
use Fuelviews\SabHeroBlog\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with(['categories', 'user', 'tags'])
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.index', [
            'posts' => $posts,
        ]);
    }

/*    public function indexMetroState(Metro $state)
    {
        $posts = Post::query()
            ->with(['categories', 'user', 'tags', 'state', 'city'])
            ->published()
            ->where('state_id', $state->id)
            ->paginate(10);

        return view('sabhero-blog::blogs.index', [
            'posts' => $posts,
            'state' => $state,
            'city' => null,
        ]);
    }

    public function indexMetroStateCity(Metro $state, Metro $city)
    {
        $posts = Post::query()
            ->with(['categories', 'user', 'tags', 'state', 'city'])
            ->published()
            ->where(['state_id' => $state->id, 'city_id' => $city->id])
            ->paginate(10);

        return view('sabhero-blog::blogs.index', [
            'posts' => $posts,
            'state' => $state,
            'city' => $city ?? null,
        ]);
    }*/

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required',
        ]);

        $searchedPosts = Post::query()
            ->with(['categories', 'user', 'tags'])
            ->published()
            ->whereAny(['title', 'sub_title'], 'like', '%'.$request->get('query').'%')
            ->paginate(10)
            ->withQueryString();

        return view('sabhero-blog::blogs.search', [
            'posts' => $searchedPosts,
            'searchMessage' => 'Search result for '.$request->get('query'),
        ]);
    }

    public function show(Post $post)
    {
        return view('sabhero-blog::blogs.show', [
            'post' => $post,
        ]);
    }

    public function showMetro(Metro $state, Metro $city, Post $post)
    {
        return view('sabhero-blog::blogs.show', [
            'post' => $post,
        ]);
    }

    public function feed()
    {
        // The feed is automatically generated using the configuration in config/feed.php
        // This controller method is just a placeholder for the route
    }
}
