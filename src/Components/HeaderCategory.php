<?php

namespace Fuelviews\SabHeroArticle\Components;

use Fuelviews\SabHeroArticle\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderCategory extends Component
{
    public function render(): View
    {
        return view('sabhero-article::components.header-category', [
            'categories' => Category::all(),
        ]);
    }
}
