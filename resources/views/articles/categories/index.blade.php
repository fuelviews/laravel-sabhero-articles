<x-sabhero-article-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <h1 class="inherits-color text-balance leading-tighter text-3xl font-semibold tracking-tight pb-8">Browse by Category</h1>
            <div class="flex flex-wrap gap-x-2">
                @foreach($categories as $category)
                    <x-sabhero-article::category-button :category="$category" size="medium" route="sabhero-article.category.post" />
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
                            <x-sabhero-article::feature-card :post="$post" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="py-8">
            <div class="container mx-auto">
                <div class="grid sm:grid-cols-3 gap-x-14 gap-y-14">
                    @foreach ($posts->skip(1) as $post)
                        <x-sabhero-article::card :post="$post" />
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
                <p class="text-2xl font-semibold text-gray-300">No categories found</p>
            </div>
        </div>
    @endif
</x-sabhero-article-layout>
