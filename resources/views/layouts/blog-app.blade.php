<x-sabhero-wrapper::layouts.app>
    <x-navigation::spacer />
    <main class="max-w-standard mx-auto px-2 xl:px-4 my-12">
        @if(!empty(Breadcrumbs::exists()))
            <section>
                <x-sabhero-blog::breadcrumb />
            </section>
        @endif
        {{ $slot }}
    </main>
</x-sabhero-wrapper::layouts.app>
