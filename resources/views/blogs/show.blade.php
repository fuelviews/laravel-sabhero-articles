<x-blog-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <div class="mx-auto mb-12 space-y-10">
                <div class="">
                    <div class="space-y-10">
                        <div>
                            <div class="flex flex-col justify-end">
                                <div class="mb-16 h-full w-full overflow-hidden bg-slate-200 rounded-2xl shadow-2xl">

                                    @if ($post->hasMedia('post_feature_image'))
                                        <img
                                            src="{{ $post->getFirstMediaUrl('post_feature_image') }}"
                                            alt="{{ $post->feature_image_alt_text }}"
                                            class="flex h-full min-h-[400px] items-center justify-center object-cover object-top"
                                            loading="eager"
                                        >
                                    @else
                                        <p>No featured image available.</p>
                                    @endif
                                </div>
                                <div class="">
                                    <p class="mb-4 text-4xl font-semibold">
                                        {{ $post->title }}
                                    </p>
                                    <p>{{ $post->sub_title }}</p>
                                </div>
                                <hr class="my-12 h-[2px] border-t-0 bg-transparent bg-gradient-to-r from-transparent via-slate-200">
                                <div>
                                    <article class="w-full mx-auto">
                                        <x-blog-markdown :content="$post->body" />
                                    </article>
                                </div>
                                <hr class="my-12 h-[2px] border-t-0 bg-transparent bg-gradient-to-r from-transparent via-slate-200">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }}" class="hover:opacity-65">
                                            <img class="h-24 w-24 overflow-hidden rounded-full border-4 border-white bg-zinc-300 object-cover text-[0] ring-1 ring-slate-300"
                                                 src="{{ $post->user->avatar }}"
                                                 alt="{{ $post->user->name() }}">
                                        </a>
                                        <div>
                                            <a href="{{ route('sabhero-blog.author.show', $post->user->slug) }}" title="{{ $post->user->name() }} "class="block max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap font-semibold hover:text-prime">
                                                {{ $post->user->name() }}
                                            </a>
                                            <span class="block whitespace-nowrap text-sm font-semibold text-zinc-600">
                                                {{ $post->formattedPublishedDate() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 sm:basis-full md:basis-auto">
                                        @foreach ($post->categories as $category)
                                            <a href="{{ route('sabhero-blog.category.post', ['category' => $category->slug]) }}">
                                                <span class="bg-prime/20 hover:bg-prime/10 inline-flex rounded-full px-3 py-2 text-sm font-semibold">
                                                    <x-heroicon-m-bars-3-center-left class="mr-1.5 inline-flex h-5 w-5 text-prime-600" />
                                                    {{ $category->name }}
                                                </span>
                                            </a>
                                        @endforeach
                                        @if($post->tags->count())
                                            @foreach ($post->tags as $tag)
                                                <a href="{{ route('sabhero-blog.tag.post', ['tag' => $tag->slug]) }}">
                                                    <span class="rounded-full inline-flex border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">
                                                        <x-heroicon-m-hashtag class="mr-1.5 inline-flex h-5 w-5 text-prime-600" />
                                                        {{ $tag->name }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <div class="relative mb-6 flex items-center gap-x-8">
                        <h2 class="whitespace-nowrap text-xl font-semibold">
                            <span class="font-bold">#</span> Related Posts
                        </h2>
                        <div class="flex w-full items-center">
                            <span class="h-[2px] w-full rounded-full bg-gradient-to-r from-slate-200 to-transparent"></span>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-x-12 gap-y-10">
                        @forelse($post->relatedPosts() as $post)
                            <x-blog-card :post="$post" />
                        @empty
                        <div class="col-span-3">
                            <p class="text-center text-xl font-semibold text-gray-300">No related posts found.</p>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </section>
</x-blog-layout>
