<x-sabhero-blog-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <h1 class="inherits-color text-balance leading-tighter text-3xl font-semibold tracking-tight pb-8">Browse by Category</h1>
            <div class="flex flex-wrap gap-x-2">
                @foreach($categories as $category)
                    <a href="{{ route('sabhero-blog.category.post', ['category' => $category->slug]) }}">
                        <span class="bg-prime/20 hover:bg-prime/10 inline-flex rounded-full px-3 py-2 text-sm font-semibold">
                            <x-heroicon-m-bars-3-center-left class="mr-1.5 inline-flex h-5 w-5 text-prime-600" />
                            {{ $category->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @if(count($posts))
        <section class="pt-8">
            <div class="container mx-auto">
                <div class="">
                    @foreach ($posts->take(1) as $post)
                        <div>
                            <x-sabhero-blog::feature-card :post="$post" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="py-8">
            <div class="container mx-auto">
                <div class="grid sm:grid-cols-3 gap-x-14 gap-y-14">
                    @foreach ($posts->skip(1) as $post)
                        <x-sabhero-blog::card :post="$post" />
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            </div>
        </section>
    @else
        <div class="container mx-auto">
            <div class="flex justify-center">
                <p class="text-2xl font-semibold text-gray-300">No posts found</p>
            </div>
        </div>
    @endif
</x-sabhero-blog-layout>
