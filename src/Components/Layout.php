<?php

namespace Fuelviews\SabHeroArticles\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Layout extends Component
{
    public $dynamicAnchorLink;

    public $componentPath;

    public $isPostView;

    public function __construct()
    {
        $route = Route::current();
        $routeName = $route->getName();
        $routeParams = $route->parameters();

        $state = $routeParams['state'] ?? null;
        $city = $routeParams['city'] ?? null;

        $stateSlug = is_object($state) ? $state->slug : $state;
        $citySlug = is_object($city) ? $city->slug : $city;

        $this->componentPath = null;
        $this->isPostView = false;

        if (Str::contains($routeName, '.show')) {
            $this->isPostView = true;
        }

        if (! $this->isPostView) {
            if ($stateSlug && $citySlug) {
                $this->componentPath = "livewire.locations.{$citySlug}";
            } elseif ($stateSlug) {
                $this->componentPath = "livewire.locations.{$stateSlug}";
            }
        }

        $this->dynamicAnchorLink = $citySlug ?? $stateSlug ?? 'default';
    }

    public function render(): View
    {
        return view('sabhero-articles::layouts.app');
    }
}
