<?php

namespace Fuelviews\SabHeroBlog\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Layout extends Component
{
    public function render(): View
    {
        return view('sabhero-blog::layouts.blog-app');
    }
}
