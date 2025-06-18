<div class="relative" wire:ignore.self>
    <button type="button" wire:click="performSearch" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors z-10">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </button>
    
    <input type="text"
           wire:model.live.debounce.300ms="search"
           wire:focus="showSuggestionsAgain"
           wire:blur="hideSuggestions"
           wire:keydown.enter="performSearch"
           placeholder="Search articles..."
           class="w-full pl-10 rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
           autocomplete="off">

    @if($showSuggestions && !empty($suggestions))
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
            @foreach($suggestions as $index => $suggestion)
                <div wire:click="selectSuggestion('{{ $suggestion['slug'] }}', '{{ addslashes($suggestion['title']) }}')"
                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                    <div class="text-sm font-medium text-gray-900 truncate">
                        {{ $suggestion['title'] }}
                    </div>
                    @if($suggestion['sub_title'])
                        <div class="text-xs text-gray-500 truncate mt-1">
                            {{ $suggestion['sub_title'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>