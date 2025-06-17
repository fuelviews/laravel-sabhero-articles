<x-sabhero-blog-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <!-- Filter Section -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('sabhero-blog.post.index') }}" class="space-y-4">
                    <!-- Filter Dropdowns -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-x-8">
                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category" class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ $selectedCategory === $category->slug ? 'selected' : '' }}>
                                        {{ ucfirst($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tag Filter -->
                        <div>
                            <label for="tag" class="block text-sm font-medium text-gray-700 mb-2">Tag</label>
                            <select name="tag" id="tag" class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Tags</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->slug }}" {{ $selectedTag === $tag->slug ? 'selected' : '' }}>
                                        {{ ucfirst($tag->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Author Filter -->
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                            <select name="author" id="author" class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Authors</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->slug }}" {{ $selectedAuthor === $author->slug ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Box with Icon -->
                        <div x-show="searchOpen" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="searchOpen = !searchOpen" 
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    :aria-expanded="searchOpen"
                                    aria-label="Toggle search"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        value="{{ request('search') }}"
                                        placeholder="Search articles..."
                                        class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @if(!request('search'))
                                            x-init="$nextTick(() => { if (searchOpen) $el.focus() })"
                                            @click.away="if (!$el.value) searchOpen = false"
                                        @endif
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    @if($selectedCategory || $selectedTag || $selectedAuthor || $searchTerm)
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-6">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>

                                @if($searchTerm)
                                    <span class="inline-flex items-center gap-x-2 rounded-full bg-purple-50 px-4 py-2 text-sm font-medium text-purple-700 border border-purple-200">
                                        Search: "{{ $searchTerm }}"
                                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="group relative h-4 w-4 rounded-full hover:bg-purple-600/20 flex items-center justify-center">
                                            <span class="sr-only">Remove filter</span>
                                            <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-purple-600/70 group-hover:stroke-purple-600">
                                                <path d="m4 4 6 6m0-6-6 6" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                    </span>
                                @endif

                                @if($selectedCategory)
                                    @php $categoryName = $categories->where('slug', $selectedCategory)->first()->name ?? $selectedCategory @endphp
                                    <span class="inline-flex items-center gap-x-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200">
                                        Category: {{ ucfirst($categoryName) }}
                                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="group relative h-4 w-4 rounded-full hover:bg-blue-600/20 flex items-center justify-center">
                                            <span class="sr-only">Remove filter</span>
                                            <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-blue-600/70 group-hover:stroke-blue-600">
                                                <path d="m4 4 6 6m0-6-6 6" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                    </span>
                                @endif

                                @if($selectedTag)
                                    @php $tagName = $tags->where('slug', $selectedTag)->first()->name ?? $selectedTag @endphp
                                    <span class="inline-flex items-center gap-x-2 rounded-full bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 border border-yellow-200">
                                        Tag: {{ ucfirst($tagName) }}
                                        <a href="{{ request()->fullUrlWithQuery(['tag' => null]) }}" class="group relative h-4 w-4 rounded-full hover:bg-yellow-600/20 flex items-center justify-center">
                                            <span class="sr-only">Remove filter</span>
                                            <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-yellow-600/70 group-hover:stroke-yellow-600">
                                                <path d="m4 4 6 6m0-6-6 6" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                    </span>
                                @endif

                                @if($selectedAuthor)
                                    @php $authorName = $authors->where('slug', $selectedAuthor)->first()->name ?? $selectedAuthor @endphp
                                    <span class="inline-flex items-center gap-x-2 rounded-full bg-green-50 px-4 py-2 text-sm font-medium text-green-700 border border-green-200">
                                        Author: {{ $authorName }}
                                        <a href="{{ request()->fullUrlWithQuery(['author' => null]) }}" class="group relative h-4 w-4 rounded-full hover:bg-green-600/20 flex items-center justify-center">
                                            <span class="sr-only">Remove filter</span>
                                            <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-green-600/70 group-hover:stroke-green-600">
                                                <path d="m4 4 6 6m0-6-6 6" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('sabhero-blog.post.index') }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-150">
                                × Reset All
                            </a>
                        </div>
                    @endif
                </form>
            </div>

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
