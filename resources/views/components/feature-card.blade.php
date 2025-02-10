@props(['post'])

<div class="grid sm:grid-cols-2 gap-y-5 gap-x-10">
    <div class="md:h-[400px] w-full overflow-hidden rounded-2xl shadow-2xl">
        @if ($post->hasMedia('post_feature_image'))
            <a href="{{ $post->state && $post->city
                    ? route('sabhero-blog.post.metro.show', [
                        'post' => $post,
                        'state' => $post->state->slug,
                        'city' => $post->city->slug,
                    ])
                    : route('sabhero-blog.post.show', ['post' => $post->slug]) }}"
                    class="hover:opacity-65">
                <img
                    src="{{ $post->getFirstMediaUrl('post_feature_image') }}"
                    alt="{{ $post->feature_image_alt_text }}"
                    class="md:h-[400px] w-full object-cover"
                    loading="eager">
            </a>
        @else
            <p>No featured image available.</p>
        @endif
    </div>
    <div class="flex flex-col justify-center space-y-10 py-4 sm:pl-10">
        <div>
            <div class="mb-5">
                @if(@isset($post->state->slug, $post->city->slug))
                    <a href="{{ route('sabhero-blog.post.metro.show', [
                            'post' => $post,
                            'state' => $post->state->slug,
                            'city' => $post->city->slug
                        ]) }}"
                       class="mb-4 block text-xl md:text-4xl font-semibold hover:text-prime">
                        {{ $post->title }}
                @else
                        <a href="{{ route('sabhero-blog.post.show', ['post' => $post->slug]) }}" class="mb-4 block text-xl md:text-4xl font-semibold hover:text-prime">
                            {{ $post->title }}
                        </a>
                @endif
                    </a>
            </div>
            <p class="mb-3 line-clamp-3">
                {!! Str::limit($post->sub_title) !!}
            </p>
            <p class="line-clamp-4">
                {{ $post->excerpt() }}
            </p>
        </div>
        <div>
            @foreach ($post->categories as $category)
                <a href="{{ route('sabhero-blog.category.post', ['category' => $category->slug]) }}">
                    <span class="bg-prime/20 hover:bg-prime/10 inline-flex rounded-full px-3 py-2 text-sm font-semibold">
                        <x-heroicon-m-bars-3-center-left class="mr-1.5 inline-flex h-5 w-5 text-prime-600" />
                        {{ $category->name }}
                    </span>
                </a>
            @endforeach
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }}" class="hover:opacity-65">
                <img class="h-14 w-14 overflow-hidden rounded-full bg-zinc-300 object-cover md:object-fill text-[0]"
                     src="{{ $post->user->avatar }}" alt="{{ $post->user->name() }}">
            </a>
            <div>
                <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }}"
                      class="block max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap font-semibold hover:text-prime">{{ $post->user->name() }}</a>
                <span class="block whitespace-nowrap text-sm font-medium text-zinc-600">
                    {{ $post->formattedPublishedDate() }}</span>
            </div>
        </div>
    </div>
</div>
