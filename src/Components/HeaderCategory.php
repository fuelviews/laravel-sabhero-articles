<?php

namespace Fuelviews\SabHeroArticles\Components;

use Fuelviews\SabHeroArticles\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderCategory extends Component
{
    public function render(): View
    {
        return view('sabhero-articles::components.header-category', [
            'categories' => Category::all(),
        ]);
    }
}
