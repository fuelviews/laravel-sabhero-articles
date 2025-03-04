@php use Fuelviews\SabHeroBlog\Enums\MetroType; @endphp
<div class="top-20 min-w-[15rem] origin-left transition duration-200 will-change-[shadow] translate-y-0">
    <div class="relative z-0 rounded-2xl border bg-white py-4 shadow-xl">
        <div class="max-h-[65vh] list-none overflow-y-auto transition-all duration-300 translate-y-0 opacity-100">
            @foreach($metros as $metro)
                @if ($metro['type'] === MetroType::STATE)
                    <a href="{{ route('sabhero-blog.post.metro.state.index', ['state' => $metro['slug']]) }}"
                       class="py-2 block text-sm font-medium transition-all duration-300 cursor-pointer hover:text-prime-600 px-6 capitalize"
                    >
                        {{ $metro['name'] }}
                    </a>
                @elseif ($metro['type'] === MetroType::CITY && $metro->parent)
                    <a href="{{ route('sabhero-blog.post.metro.state.city.index', ['state' => $metro->parent->slug, 'city' => $metro['slug']]) }}"
                       class="py-2 block text-sm font-medium transition-all duration-300 cursor-pointer hover:text-prime-600 px-6 capitalize"
                    >
                        {{ $metro['name'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
