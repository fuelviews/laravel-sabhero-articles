<?php

namespace Fuelviews\SabHeroArticles\Http\Controllers;

use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request, $page = 1)
    {
        $query = Post::query()
            ->with(['categories', 'user', 'tags'])
            ->published();

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by author
        if ($request->filled('author')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('slug', $request->author);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->whereAny(['title', 'sub_title'], 'like', '%'.$request->search.'%');
        }

        // Set current page for pagination
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $posts = $query->paginate(10)->withQueryString();

        // Get all categories, tags, and authors for the filter dropdowns
        $categories = \Fuelviews\SabHeroArticles\Models\Category::whereHas('posts', function ($q) {
            $q->published();
        })->orderBy('name')->get();

        $tags = \Fuelviews\SabHeroArticles\Models\Tag::whereHas('posts', function ($q) {
            $q->published();
        })->orderBy('name')->get();

        $userModel = config('sabhero-articles.user.model');
        $authors = $userModel::authors()
            ->whereHas('posts', function ($q) {
                $q->published();
            })
            ->orderBy('name')
            ->get();

        return view('sabhero-articles::articles.index', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'authors' => $authors,
            'selectedCategory' => $request->category,
            'selectedTag' => $request->tag,
            'selectedAuthor' => $request->author,
            'searchTerm' => $request->search,
        ]);
    }

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

        return view('sabhero-articles::articles.search', [
            'posts' => $searchedPosts,
            'searchMessage' => 'Search result for '.$request->get('query'),
        ]);
    }

    public function show(Post $post)
    {
        return view('sabhero-articles::articles.show', [
            'post' => $post,
        ]);
    }

    public function feed()
    {
        // The feed is automatically generated using the configuration in config/feed.php
        // This controller method is just a placeholder for the route
    }
}
