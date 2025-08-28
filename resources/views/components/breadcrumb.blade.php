@props(['breadcrumbs' => null])

@php
    if (!$breadcrumbs) {
        $breadcrumbs = \Diglactic\Breadcrumbs\Breadcrumbs::generate();
    }
@endphp

@if ($breadcrumbs && $breadcrumbs->count())
    <nav aria-label="breadcrumb">
        <ol class="flex flex-wrap md:flex-nowrap items-center gap-2">
            @foreach ($breadcrumbs as $crumb)
                <li class="inline-flex items-center">
                    @if (property_exists($crumb, 'icon'))
                        <a href="{{ $crumb->url }}" class="inline-block w-5 h-5 mr-1 text-gray-500 hover:text-prime-700">
                            {!! $crumb->icon !!}
                         </a>
                    @endif

                    @if($crumb->url && !$loop->last)
                        <a href="{{ $crumb->url }}" class="text-gray-600 hover:text-prime-700 {{ $loop->first ? '' : 'truncate max-w-32 sm:max-w-48 md:max-w-64' }}" >
                            {{ $crumb->title }}
                        </a>
                    @else
                        <a href="{{ $crumb->url }}" class="text-gray-500 hover:text-prime-700 truncate max-w-[200px] md:max-w-[500px] lg:max-w-[800px]">
                            {{ $crumb->title }}
                        </a>
                    @endif

                    @if(!$loop->last)
                        <span class="ml-2 text-gray-400">/</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
