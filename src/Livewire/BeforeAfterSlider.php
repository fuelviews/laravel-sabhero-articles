<?php

namespace Fuelviews\SabHeroBlog\Livewire;

use Fuelviews\SabHeroBlog\Enums\PortfolioType;
use Fuelviews\SabHeroBlog\Models\Portfolio;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class BeforeAfterSlider extends Component
{
    public $portfolioItems = [];
    public $portfolioType = null;

    public function mount($type = null)
    {
        $this->portfolioType = $type;
        
        $query = Portfolio::where('is_published', true);
        
        if ($this->portfolioType) {
            $query->where(function (Builder $query) {
                $query->where('type', $this->portfolioType)
                      ->orWhere('type', PortfolioType::ALL->value);
            });
        }
        
        $this->portfolioItems = $query->orderBy('order')->get();
    }

    public function render()
    {
        return view('sabhero-blog::livewire.before-after-slider');
    }
}
