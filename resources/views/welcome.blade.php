<x-app-layout>
    <x-slot name="title">
        Beranda
    </x-slot>

    <div class="bg-blue-600 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">
                Selamat Datang di Pelayanan Kami
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-blue-100">
                Temukan informasi terbaru, jadwal kegiatan, dan renungan firman Tuhan bersama jemaat di sini.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Jadwal Terdekat</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Lihat Semua Agenda &rarr;</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($agendas as $agenda)
                <div class="bg-white rounded-lg shadow border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 flex-grow">
                        <div class="text-sm font-semibold text-blue-600 mb-1">
                            {{ $agenda->start_time->format('d M Y') }} • {{ $agenda->start_time->format('H:i') }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $agenda->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit(strip_tags($agenda->description), 80) }}</p>
                        
                        @if($agenda->location)
                            <div class="flex items-center text-sm text-gray-500 mt-auto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $agenda->location }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
                    Belum ada jadwal kegiatan terdekat.
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Berita & Artikel</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Indeks Berita &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($posts as $post)
                    <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                Tanpa Gambar
                            </div>
                        @endif
                        <div class="p-6 flex-grow flex flex-col">
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">
                                {{ $post->category?->name ?? 'Uncategorized' }}
                            </div>
                            <a href="{{ route('posts.show', $post->slug) }}" class="block mt-2">
    <h3 class="text-xl font-semibold text-gray-900 hover:text-blue-600 transition">{{ $post->title }}</h3>
</a>
                            <p class="mt-3 text-sm text-gray-500 flex-grow">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <div class="mt-4 text-xs text-gray-400">
                                {{ $post->published_at ? $post->published_at->format('d M Y') : '' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
                        Belum ada berita atau artikel yang dipublikasikan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</x-app-layout>