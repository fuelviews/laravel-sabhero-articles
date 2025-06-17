@props(['category', 'size' => 'small', 'route' => 'sabhero-blog.post.index'])

@php
    $sizeClasses = match($size) {
        'medium' => 'px-3 py-2 text-sm',
        default => 'px-2 py-1.5 text-xs'
    };

    $iconClasses = match($size) {
        'medium' => 'mr-1.5 h-4 w-4',
        default => 'mr-1 h-3 w-3'
    };

    $routeParams = $route === 'sabhero-blog.category.post'
        ? ['category' => $category->slug]
        : ['category' => $category->slug];
@endphp

<a href="{{ route($route, $routeParams) }}" {{ $attributes }}>
    <span
        class="border border-prime-300 hover:bg-prime-100/15 text-slate-700 inline-flex items-center rounded-full font-semibold cursor-pointer transition-colors {{ $sizeClasses }}">
        <x-heroicon-m-bars-3-center-left class="inline-flex text-prime {{ $iconClasses }}"/>
        {{ $category->name }}
    </span>
</a>
