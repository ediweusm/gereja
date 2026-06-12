<x-app-layout>
    <x-slot name="title">
        {{ $post->title }}
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 text-center">
                <div class="text-sm font-semibold tracking-wide text-blue-600 uppercase mb-2">
                    {{ $post->category?->name ?? 'Uncategorized' }}
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-4">
                    {{ $post->title }}
                </h1>
                <div class="text-gray-500 text-sm flex justify-center items-center gap-4">
                    <span>Diterbitkan: {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                    <span>•</span>
                    <span>Oleh: {{ $post->user?->name ?? 'Admin' }}</span>
                </div>
            </div>

            @if($post->thumbnail)
                <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[500px] object-cover">
                </div>
            @endif

            <article class="prose prose-blue prose-lg max-w-none text-gray-700">
                {!! $post->content !!}
            </article>

            <div class="mt-12 pt-8 border-t border-gray-200">
                <a href="/" class="inline-flex items-center font-medium text-blue-600 hover:text-blue-500">
                    &larr; Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-app-layout>