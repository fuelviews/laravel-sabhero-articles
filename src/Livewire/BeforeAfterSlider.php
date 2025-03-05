<?php

namespace Fuelviews\SabHeroBlog\Livewire;

use Livewire\Component;
use Fuelviews\SabHeroBlog\Models\Portfolio;

class BeforeAfterSlider extends Component
{
    public $portfolioItems = [];

    public function mount()
    {
        $this->portfolioItems = Portfolio::where('is_published', true)
            ->orderBy('order')
            ->get();
    }

    public function render()
    {
        return view('sabhero-blog::livewire.before-after-slider');
    }
}
