@props([
    'categories' => collect(),
    'tags' => collect(),
    'authors' => collect(),
    'selectedCategory' => null,
    'selectedTag' => null,
    'selectedAuthor' => null,
    'searchTerm' => null,
])

@php
    $hasActiveFilters = $selectedCategory || $selectedTag || $selectedAuthor || $searchTerm;
    $activeFilterCount = collect([$selectedCategory ? 1 : 0, $selectedTag ? 1 : 0, $selectedAuthor ? 1 : 0, $searchTerm ? 1 : 0])->sum();
@endphp

<div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg">
    <div class="p-4 sm:p-5 lg:p-6">
        <h2 class="hidden lg:block text-lg font-medium text-gray-900 mb-4">Search & Filter</h2>

        <form method="GET" action="{{ route('sabhero-articles.post.index') }}" class="space-y-4">
            <!-- Mobile Search Only -->
            <div class="lg:hidden">
                <div>
                    <label for="search-mobile" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    @if(class_exists('\Livewire\Livewire') && !app()->environment('testing'))
                        <livewire:sabhero-articles::search-autocomplete :initial-search="$searchTerm" />
                    @else
                        <div class="relative">
                            <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors z-10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <input type="text"
                                   name="search"
                                   id="search-mobile"
                                   value="{{ $searchTerm }}"
                                   placeholder="Search articles..."
                                   class="w-full pl-10 rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                   onkeypress="if(event.key==='Enter'){this.form.submit()}">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Desktop Filters and Search -->
            <div class="hidden lg:flex lg:flex-row lg:items-start">
                <!-- Search Box -->
                <div class="w-auto lg:w-64 lg:pr-6 self-start">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    @if(class_exists('\Livewire\Livewire') && !app()->environment('testing'))
                        <livewire:sabhero-articles::search-autocomplete :initial-search="$searchTerm" />
                    @else
                        <div class="relative">
                            <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors z-10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ $searchTerm }}"
                                   placeholder="Search articles..."
                                   class="w-full pl-10 rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                   onkeypress="if(event.key==='Enter'){this.form.submit()}">
                        </div>
                    @endif
                </div>

                <!-- Separator -->
                <div class="w-px bg-gray-300 self-stretch"></div>

                <!-- Filter Dropdowns Group -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1 lg:pl-6">
                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category"
                                id="category"
                                class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                onchange="this.form.submit()">
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
                        <select name="tag"
                                id="tag"
                                class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                onchange="this.form.submit()">
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
                        <select name="author"
                                id="author"
                                class="w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                onchange="this.form.submit()">
                            <option value="">All Authors</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->slug }}" {{ $selectedAuthor === $author->slug ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($hasActiveFilters)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 gap-4">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                        <span class="text-sm text-gray-700">Active filters:</span>

                        @if($searchTerm)
                            <span class="inline-flex items-center rounded-full bg-purple-50 px-2 py-1 text-sm text-gray-700 border border-purple-200">
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
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-sm text-gray-700 border border-blue-200">
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
                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-sm text-gray-700 border border-yellow-200">
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
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-sm text-gray-700 border border-green-200">
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

                    <button type="button" onclick="window.location.href='{{ route('sabhero-articles.post.index') }}'" class="inline-flex items-center px-3 py-1 text-sm text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-150 self-start sm:self-auto">
                        × Reset All
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
