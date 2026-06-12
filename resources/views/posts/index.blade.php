<x-app-layout>
    <x-slot name="title">
        Berita & Artikel
    </x-slot>

    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Berita & Artikel Jemaat
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Kumpulan informasi, warta kegiatan, dan renungan terbaru.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($posts as $post)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-52 object-cover">
                        @else
                            <div class="w-full h-52 bg-gray-200 flex items-center justify-center text-gray-400">
                                Tanpa Gambar
                            </div>
                        @endif
                        <div class="p-6 flex-grow flex flex-col">
                            <div class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-2">
                                {{ $post->category?->name ?? 'Uncategorized' }}
                            </div>
                            <a href="{{ route('posts.show', $post->slug) }}" class="block mt-2">
                                <h2 class="text-xl font-bold text-gray-900 hover:text-blue-600 transition leading-tight">{{ $post->title }}</h2>
                            </a>
                            <p class="mt-3 text-sm text-gray-600 flex-grow">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                                <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 font-medium hover:underline">Baca &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                        Belum ada berita atau artikel yang dipublikasikan.
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>

        </div>
    </div>
</x-app-layout>