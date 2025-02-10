<?php

namespace Fuelviews\SabHeroBlog\Components;

use Fuelviews\SabHeroBlog\Models\Metro;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class HeaderMetro extends Component
{
    public function render(): View
    {
        return view('sabhero-blog::components.header-metro', [
            'metros' => Metro::all(),
        ]);
    }
}
