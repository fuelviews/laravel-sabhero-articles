@php
    $route = Route::current();
    $routeName = $route->getName();
    $routeParams = $route->parameters();

    $state = $routeParams['state'] ?? null;
    $city = $routeParams['city'] ?? null;

    $stateSlug = is_object($state) ? $state->slug : $state;
    $citySlug = is_object($city) ? $city->slug : $city;

    $componentPath = null;
    $isPostView = false;

    if (Str::contains($routeName, '.show')) {
        $isPostView = true;
    }

    if (!$isPostView) {
        if ($stateSlug && $citySlug) {
            $componentPath = "livewire.locations.{$citySlug}";
        }
        elseif ($stateSlug) {
            $componentPath = "livewire.locations.{$stateSlug}";
        }
    }

    $dynamicAnchorLink = $citySlug ?? $stateSlug ?? 'default';
@endphp

<x-sabhero-wrapper::layouts.app>
    @if(is_null($componentPath) && ! View::exists($componentPath))
        <x-navigation::spacer />
    @endif
    <main>
        @if($componentPath && View::exists($componentPath))
            @include($componentPath)
        @endif
        <section class="max-w-standard mx-auto px-2 xl:px-4 my-12">
            <a id="{{ $dynamicAnchorLink }}" class="{{ config('sabhero-blog.heading_permalink.html_class') }}"></a>
            @if(!empty(Breadcrumbs::exists()))
                <section>
                    <x-sabhero-blog::breadcrumb />
                </section>
            @endif
            {{ $slot }}
        </section>
    </main>
</x-sabhero-wrapper::layouts.app>
