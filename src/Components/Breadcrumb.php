<?php

namespace Fuelviews\SabHeroArticle\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public function render(): View|Closure|string
    {
        return view('sabhero-article::components.breadcrumb');
    }
}
