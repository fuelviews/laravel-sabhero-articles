<x-sabhero-wrapper::layouts.app>
    @if(is_null($componentPath) && ! \Illuminate\Support\Facades\View::exists($componentPath))
        <x-navigation::spacer />
    @endif
    <main>
        @if($componentPath && \Illuminate\Support\Facades\View::exists($componentPath))
            @include($componentPath)
        @endif
        <section class="max-w-standard mx-auto px-2 sm:px-3 md:px-4 lg:px-4 xl:px-4 my-2 md:my-6 lg:my-12">
            <a id="{{ $dynamicAnchorLink }}" class="{{ config('sabhero-articles.heading_permalink.html_class') }}"></a>
            @if(!empty(\Diglactic\Breadcrumbs\Breadcrumbs::exists()))
                <section>
                    <x-sabhero-articles::breadcrumb />
                </section>
            @endif
            {{ $slot }}
        </section>
    </main>
</x-sabhero-wrapper::layouts.app>
