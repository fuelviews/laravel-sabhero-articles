@props(['searchTerm' => '', 'id' => 'search'])

<div class="relative">
    <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors z-10">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </button>
    <input type="text"
           name="search"
           id="{{ $id }}"
           value="{{ $searchTerm }}"
           placeholder="Search articles..."
           class="w-full pl-10 rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
           onkeypress="if(event.key==='Enter'){this.form.submit()}"
           {{ $attributes }}>
</div>