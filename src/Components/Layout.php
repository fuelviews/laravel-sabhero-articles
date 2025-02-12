<?php

namespace Fuelviews\SabHeroBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View
    {
        return view('sabhero-blog::layouts.blog-app');
    }
}
