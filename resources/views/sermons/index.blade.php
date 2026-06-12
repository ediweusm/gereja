<x-app-layout>
    <x-slot name="title">
        Arsip Khotbah
    </x-slot>

    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Arsip Khotbah & Pengajaran
                </h1>
                <p class="mt-3 text-xl text-gray-500">
                    Dengarkan dan pelajari kembali firman Tuhan yang telah disampaikan.
                </p>
            </div>

            <div class="space-y-6">
                @forelse ($sermons as $sermon)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $sermon->title }}</h2>
                                <div class="text-sm font-medium text-blue-600 mt-1">
                                    {{ $sermon->preacher ?? 'Hamba Tuhan' }} • {{ $sermon->sermon_date->format('d M Y') }}
                                </div>
                            </div>
                            @if($sermon->passage)
                                <div class="bg-gray-100 px-4 py-2 rounded-lg text-sm font-semibold text-gray-700">
                                    📖 {{ $sermon->passage }}
                                </div>
                            @endif
                        </div>

                        <div class="text-gray-600 mb-6 line-clamp-3">
                            {{ $sermon->content_summary }}
                        </div>

                        <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4">
                            @if($sermon->video_url)
                                <a href="{{ $sermon->video_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a10 10 0 100 20 10 10 0 000-20zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                    Tonton Video
                                </a>
                            @endif
                            
                            @if($sermon->audio_url)
                                <a href="{{ $sermon->audio_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 transition">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                                    Dengarkan Audio
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                        Belum ada arsip khotbah yang diunggah.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $sermons->links() }}
            </div>
        </div>
    </div>
</x-app-layout>