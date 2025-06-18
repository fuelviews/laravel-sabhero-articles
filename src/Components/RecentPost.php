<?php

namespace Fuelviews\SabHeroArticle\Components;

use Fuelviews\SabHeroArticle\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecentPost extends Component
{
    public function render(): View
    {
        $posts = Post::query()->published()->whereNot('slug', request('post')->slug)->latest()->take(5)->get();

        return view('sabhero-article::components.recent-post', [
            'posts' => $posts,
        ]);
    }
}
