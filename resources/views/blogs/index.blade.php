<x-sabhero-blog-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <!-- Filter Section -->
            <x-sabhero-blog::blog-filters 
                :categories="$categories"
                :tags="$tags"
                :authors="$authors"
                :selected-category="$selectedCategory"
                :selected-tag="$selectedTag"
                :selected-author="$selectedAuthor"
                :search-term="$searchTerm"
            />

            <h1 class="text-4xl font-bold mb-8">Articles</h1>
        </div>
    </section>

    @if(count($posts))
        @if($posts->total() > 0)
            <section class="py-8">
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
        @endif

        @if($posts->count() > 1)
            <section class="py-8">
                <div class="container mx-auto">
                    <div class="grid sm:grid-cols-3 gap-x-14 gap-y-14">
                        @foreach ($posts->skip(1) as $post)
                            <x-sabhero-blog::card :post="$post" />
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $posts->links('sabhero-blog::pagination.tailwind') }}
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="py-16">
            <div class="container mx-auto">
                <div class="flex flex-col items-center text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-2xl font-semibold text-gray-900">No articles found</h3>
                    <p class="mt-2 text-gray-500">No articles match your current filter selection. Try adjusting your filters above.</p>
                </div>
            </div>
        </section>
    @endif
</x-sabhero-blog-layout>
