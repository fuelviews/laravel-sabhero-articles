@props(['post'])

<div class="flex flex-col gap-y-8 h-full">
    <div class="h-[250px] w-full rounded-xl overflow-hidden shadow-xl">
        @if ($post->hasMedia('post_feature_image'))
            <a href="{{ route('sabhero-article.post.show', ['post' => $post->slug]) }}"
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
    <div class="flex flex-col px-2 flex-1">
        <div>
            <h2 title="{{ $post->title }}"
                class="hover:text-prime mb-3 line-clamp-2 text-xl font-semibold">
                <a href="{{ route('sabhero-article.post.show', ['post' => $post->slug]) }}">
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
        <div class="mt-auto">
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($post->categories->take(1) as $category)
                    <x-sabhero-article::category-button :category="$category" size="small" />
                @endforeach
                @foreach ($post->tags->take(1) as $tag)
                    <x-sabhero-article::tag-button :tag="$tag" size="small" />
                @endforeach
            </div>
            <div class="mt-4 md:mt-6">
                <x-sabhero-article::author-profile :user="$post->user" :post="$post" size="medium" />
            </div>
        </div>
    </div>
</div>
