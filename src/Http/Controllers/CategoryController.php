<?php

namespace Fuelviews\SabHeroBlog\Http\Controllers;

use Fuelviews\SabHeroBlog\Models\Category;
use Fuelviews\SabHeroBlog\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function posts(Request $request, Category $category)
    {
        $posts = $category->load(['posts.user', 'posts.categories'])
            ->posts()
            ->published()
            ->paginate(10);

        return view('sabhero-blog::blogs.categories.show', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function allCategories()
    {
        $posts = Post::published()
            ->with(['categories', 'user', 'tags', 'state', 'city'])
            ->latest()
            ->paginate(10);

        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('sabhero-blog::blogs.categories.index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
