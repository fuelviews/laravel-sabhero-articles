<?php

namespace Fuelviews\SabHeroBlog\Components;

use Fuelviews\SabHeroBlog\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class HeaderCategory extends Component
{
    public function render(): View
    {
        return view('sabhero-blog::components.header-category', [
            'categories' => Category::all(),
        ]);
    }
}
