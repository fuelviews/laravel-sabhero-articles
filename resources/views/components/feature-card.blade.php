@props(['post'])

<div class="grid sm:grid-cols-2 gap-y-5 gap-x-10">
    <div class="md:h-[400px] w-full overflow-hidden rounded-2xl shadow-2xl">
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
    <div class="flex flex-col justify-between py-4 sm:pl-10 h-full">
        <div>
            <div class="mb-5">
                <a href="{{ route('sabhero-blog.post.show', ['post' => $post->slug]) }}" class="mb-4 block text-xl md:text-4xl font-semibold hover:text-prime">
                    {{ $post->title }}
                </a>
            </div>
            <p class="mb-3 line-clamp-3">
                {!! Str::limit($post->sub_title) !!}
            </p>
            <p class="line-clamp-4">
                {{ $post->excerpt() }}
            </p>
        </div>
        <div class="mt-auto space-y-4">
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($post->categories->take(1) as $category)
                    <x-sabhero-blog::category-button :category="$category" size="medium" />
                @endforeach
                @foreach ($post->tags->take(1) as $tag)
                    <x-sabhero-blog::tag-button :tag="$tag" size="medium" />
                @endforeach
            </div>
            <div class="pt-1">
                <x-sabhero-blog::author-profile :user="$post->user" :post="$post" size="large" />
            </div>
        </div>
    </div>
</div>
