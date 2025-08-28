<?php

namespace Fuelviews\SabHeroArticles\Http\Controllers;

use Fuelviews\SabHeroArticles\Models\Category;
use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function posts(Request $request, Category $category)
    {
        $posts = $category->load(['posts.user', 'posts.categories'])
            ->posts()
            ->published()
            ->paginate(10);

        return view('sabhero-articles::articles.categories.show', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function allCategories()
    {
        $posts = Post::published()
            ->with(['categories', 'user', 'tags'])
            ->latest()
            ->paginate(10);

        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('sabhero-articles::articles.categories.index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
