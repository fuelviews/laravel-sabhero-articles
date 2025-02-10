@props(['post'])

<div class="flex flex-col gap-y-8">
    <div class="h-[250px] w-full rounded-xl overflow-hidden shadow-xl">
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
    <div class="flex flex-col justify-between space-y-3 px-2">
        <div>
            <h2 title="{{ $post->title }}"
                class="hover:text-prime mb-3 line-clamp-2 text-xl font-semibold">
                <a href="{{ $post->state && $post->city
                    ? route('sabhero-blog.post.metro.show', [
                        'post' => $post,
                        'state' => $post->state->slug,
                        'city' => $post->city->slug,
                    ])
                    : route('sabhero-blog.post.show', ['post' => $post->slug]) }}">
                    {{ $post->title }}
                </a>
            </h2>
            <p class="mb-3 line-clamp-3">
                {{ Str::limit($post->sub_title, 100) }}
            </p>
            <p class="mb-3 line-clamp-3">
                {{ $post->excerpt() }}
            </p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }}" class="hover:opacity-65">
                <img class="h-10 w-10 overflow-hidden rounded-full bg-zinc-300 object-cover text-[0]"
                     src="{{ $post->user->avatar }}" alt="{{ $post->user->name() }}">
            </a>
            <div>
                <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }}"
                      class="block max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap text-sm font-semibold hover:text-prime">
                      {{ $post->user->name() }}
                </a>
                <span
                    class="block whitespace-nowrap text-sm font-medium text-zinc-600">
                    {{ $post->formattedPublishedDate() }}
                </span>
            </div>
        </div>
    </div>
</div>
