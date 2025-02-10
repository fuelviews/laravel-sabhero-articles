<?php

namespace Fuelviews\SabHeroBlog\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('sabhero-blog::components.breadcrumb');
    }
}
