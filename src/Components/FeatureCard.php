<?php

namespace Fuelviews\SabHeroArticles\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeatureCard extends Component
{
    public function render(): View
    {
        return view('sabhero-articles::components.feature-card');
    }
}
