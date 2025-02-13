<x-sabhero-blog-layout>
    @if(count($posts))
    <section class="py-8">
        <div class="container mx-auto">
            <div class="">
                @foreach ($posts->take(1) as $post)
                    <div>
                        <x-sabhero-blog::feature-card :post="$post" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="py-8">
        <div class="container mx-auto">
            <div class="grid sm:grid-cols-3 gap-x-14 gap-y-14">
                @foreach ($posts->skip(1) as $post)
                    <x-sabhero-blog::card :post="$post" />
                @endforeach
            </div>
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </section>
    @else
    <div class="container mx-auto">
        <div class="flex justify-center">
            <p class="text-2xl font-semibold text-gray-300">No posts found</p>
        </div>
    </div>
    @endif
</x-sabhero-blog-layout>
