<?php

namespace Fuelviews\SabHeroBlog\Components;

use Fuelviews\SabHeroBlog\Models\Metro;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderMetro extends Component
{
    public function render(): View
    {
        return view('sabhero-blog::components.header-metro', [
            'metros' => Metro::all(),
        ]);
    }
}
