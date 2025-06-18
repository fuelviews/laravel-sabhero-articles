@props(['tag', 'size' => 'small', 'route' => 'sabhero-article.post.index'])

@php
    $sizeClasses = match($size) {
        'medium' => 'px-3 py-2 text-sm',
        default => 'px-2 py-1.5 text-xs'
    };

    $iconClasses = match($size) {
        'medium' => 'mr-1.5 h-4 w-4',
        default => 'mr-1 h-3 w-3'
    };

    $routeParams = $route === 'sabhero-article.tag.post'
        ? ['tag' => $tag->slug]
        : ['tag' => $tag->slug];
@endphp

<a href="{{ route($route, $routeParams) }}" {{ $attributes }}>
    <span
        class="rounded-full inline-flex items-center border border-alt-300 font-semibold text-slate-700 hover:bg-alt-100/15 cursor-pointer transition-colors {{ $sizeClasses }}">
        <x-heroicon-m-hashtag class="inline-flex text-alt {{ $iconClasses }}"/>
        {{ $tag->name }}
    </span>
</a>
