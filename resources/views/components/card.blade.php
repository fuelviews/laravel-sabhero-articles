@props(['post'])

<div class="flex flex-col gap-y-8">
    <div class="h-[250px] w-full rounded-xl overflow-hidden shadow-xl">
        @if ($post->hasMedia('post_feature_image'))
            <a href="{{ route('sabhero-blog.post.show', ['post' => $post->slug]) }}"
                    class="hover:opacity-65">
                <img
                    srcset="{{ $post->getFirstMedia('post_feature_image')->getSrcset() }}"
                    src="{{ $post->getFirstMedia('post_feature_image')->getUrl() }}"
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
                <a href="{{ route('sabhero-blog.post.show', ['post' => $post->slug]) }}">
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
        <div class="mb-3 flex flex-wrap gap-2">
            @foreach ($post->categories as $category)
                <a href="{{ route('sabhero-blog.post.index', ['category' => $category->slug]) }}">
                    <span class="bg-prime/10 hover:bg-prime/15 inline-flex rounded-full px-2 py-1.5 text-xs font-semibold cursor-pointer transition-colors">
                        <x-heroicon-m-bars-3-center-left class="mr-1.5 inline-flex h-5 w-5 text-prime" />
                        {{ $category->name }}
                    </span>
                </a>
            @endforeach
            @if($post->tags->count())
                @foreach ($post->tags as $tag)
                    <a href="{{ route('sabhero-blog.post.index', ['tag' => $tag->slug]) }}">
                        <span class="rounded-full inline-flex border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer transition-colors">
                            <x-heroicon-m-hashtag class="mr-1.5 inline-flex h-5 w-5 text-prime-700" />
                            {{ $tag->name }}
                        </span>
                    </a>
                @endforeach
            @endif
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('sabhero-blog.author.show', ['user' => $post->user->slug]) }}" title="{{ $post->user->name() }}" class="hover:opacity-65">

                <img
                    class="h-10 w-10 overflow-hidden rounded-full object-cover text-[0]"
                    srcset="{{ $post->user->getAuthorMediaSrcSet() }}"
                    src="{{ $post->user->getAuthorAvatarUrl() }}"
                    alt="{{ $post->user->name() }}"
                >
            </a>
            <div>
                <a href="{{ route('sabhero-blog.author.show', ['user' => $post->user->slug]) }}" title="{{ $post->user->name() }}"
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
