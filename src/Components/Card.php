<?php

namespace Fuelviews\SabHeroArticles\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public function render(): View
    {
        return view('sabhero-articles::components.card');
    }
}
