<x-sabhero-article-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row md:items-center gap-8">
                <div class="flex-shrink-0">
                    <a href="{{ route('sabhero-article.author.show', $author->slug) }}">
                        <img
                            class="h-48 w-48 rounded-full shadow-lg"
                            srcset="{{ $author->getAuthorMediaSrcSet() }}"
                            src="{{ $author->getAuthorAvatarUrl() }}"
                            alt="{{ $author->name }}"
                        >
                    </a>
                </div>
                <div class="flex-grow">
                    <h1 class="text-2xl md:text-4xl font-semibold">{{ $author->name }}</h1>
                    <p class="mt-8 text-base leading-7 text-gray-600">{{ $author->bio }}</p>
                    @isset($author->links)
                        <ul role="list" class="flex list-none flex-wrap items-center gap-x-2 mt-8">
                            @foreach ($author->links as $link)
                                <li>
                                    <a href="{{ $link['url'] }}"
                                       class="text-gray-400 hover:text-gray-500"
                                       title="{{ $link['label'] }}">
                                        @if (str_contains(strtolower($link['label']), 'x') || str_contains(strtolower($link['url']), 'x.com'))
                                            <svg
                                                class="h-4 w-4"
                                                fill="currentColor"
                                                viewBox="0 0 512 512"
                                                aria-hidden="true">
                                                <path
                                                    d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                                            </svg>
                                        @elseif (str_contains(strtolower($link['label']), 'linkedin') || str_contains(strtolower($link['url']), 'linkedin'))
                                            <svg
                                                class="h-5 w-5"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                                aria-hidden="true">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <!-- Default link icon -->
                                            <svg
                                                class="h-5 w-5"
                                                fill="currentColor"
                                                viewBox="0 0 512 512">
                                                <path
                                                    d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H8.1C38 85.6 101.7 28.8 180 8.1C154.5 42.3 134.7 95.8 124.7 160h0zm109.6 0c-6.3-35.6-15.4-66.5-26.9-91.9C219.7 52.3 208.9 37.8 200 32.4C191.1 37.8 180.3 52.3 175.3 68.1c-11.5 25.4-20.6 56.3-26.9 91.9H283.3zm-16.1 224c-11.4-25.9-16.9-53.9-16.9-83.3c0-14.9 1.3-29.3 3.6-43.2H334c-2.3 13.9-3.6 28.3-3.6 43.2c0 29.4 5.5 57.4 16.9 83.3H267.2zm22.7 16c11.2 25.4 15.7 53.9 15.7 83.3c0 14.9-1.3 29.3-3.6 43.2H178c2.3-13.9 3.6-28.3 3.6-43.2c0-29.4-4.5-57.9-15.7-83.3H289.9z" />
                                            </svg>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endisset
                </div>
            </div>
        </div>
    </section>
    @if(count($posts))
        <section class="py-8">
            <div class="container mx-auto">
                <div class="">
                    @foreach ($posts->take(1) as $post)
                        <div>
                            <x-sabhero-article::feature-card :post="$post" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="py-8">
            <div class="container mx-auto">
                <div class="grid sm:grid-cols-3 gap-x-14 gap-y-14">
                    @foreach ($posts->skip(1) as $post)
                        <x-sabhero-article::card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="container mx-auto">
            <div class="flex justify-center pb-8">
                <p class="text-2xl font-semibold text-gray-300">No posts found</p>
            </div>
        </div>
    @endif
</x-sabhero-article-layout>
