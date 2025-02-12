<x-blog-layout>
    <section class="py-8">
        <div class="container mx-auto">
            <h1 class="text-3xl font-semibold mb-8">Our Authors</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($authors as $author)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <a href="{{ route('sabhero-blog.author.show', ['author' => $author->slug]) }}">
                            <img src="{{ $author->avatar }}" class="h-48 w-48 rounded-full mx-auto hover:opacity-65" alt="{{ $author->name }}">
                            <h2 class="mt-4 text-xl font-semibold text-center hover:text-prime">{{ $author->name }}</h2>
                        </a>
                        <p class="text-center mt-2 text-gray-600">{{ $author->posts_count }} {{ Str::plural('post', $author->posts_count) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $authors->links() }}
            </div>
        </div>
    </section>
</x-blog-layout>
