<?php

namespace Fuelviews\SabHeroArticle\Http\Livewire;

use Fuelviews\SabHeroArticle\Models\Post;
use Livewire\Component;

class SearchAutocomplete extends Component
{
    public $search = '';
    public $suggestions = [];
    public $showSuggestions = false;
    public $highlightIndex = -1;

    public function mount($initialSearch = '')
    {
        $this->search = $initialSearch;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->suggestions = Post::published()
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('sub_title', 'like', '%' . $this->search . '%');
                })
                ->limit(8)
                ->get(['id', 'title', 'slug', 'sub_title'])
                ->toArray();
            
            $this->showSuggestions = count($this->suggestions) > 0;
            $this->highlightIndex = -1;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function selectSuggestion($slug, $title)
    {
        $this->search = $title;
        $this->showSuggestions = false;
        $this->suggestions = [];
        
        // Redirect to the post
        return redirect()->route('sabhero-article.post.show', ['post' => $slug]);
    }

    public function performSearch()
    {
        if (! empty($this->search)) {
            return redirect()->route('sabhero-article.post.index', ['search' => $this->search]);
        }
    }

    public function hideSuggestions()
    {
        // Delay hiding to allow click events on suggestions
        $this->showSuggestions = false;
    }

    public function showSuggestionsAgain()
    {
        if (! empty($this->suggestions)) {
            $this->showSuggestions = true;
        }
    }

    public function render()
    {
        return view('sabhero-article::livewire.search-autocomplete');
    }
}
