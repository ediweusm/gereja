<x-app-layout>
    <x-slot name="title">
        Galeri Dokumentasi
    </x-slot>

    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Galeri Dokumentasi
                </h1>
                <p class="mt-3 text-xl text-gray-500">
                    Momen-momen pelayanan dan kebersamaan jemaat.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                @forelse ($galleries as $gallery)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 overflow-hidden flex flex-col">
                        @php
                            // Ambil gambar pertama dari array JSON images
                            $firstImage = is_array($gallery->images) && count($gallery->images) > 0 ? $gallery->images[0] : null;
                        @endphp
                        
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $gallery->title }}" class="w-full h-56 object-cover">
                        @else
                            <div class="w-full h-56 bg-gray-200 flex items-center justify-center text-gray-400">
                                Tanpa Gambar
                            </div>
                        @endif
                        <div class="p-6 flex-grow flex flex-col">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $gallery->title }}</h2>
                            <p class="text-sm text-gray-600 flex-grow">{{ Str::limit($gallery->description, 100) }}</p>
                            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $gallery->created_at->format('d M Y') }}</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                                    {{ is_array($gallery->images) ? count($gallery->images) : 0 }} Foto
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                        Belum ada album foto yang diunggah.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $galleries->links() }}
            </div>
        </div>
    </div>
</x-app-layout>