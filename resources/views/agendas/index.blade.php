<x-app-layout>
    <x-slot name="title">
        Agenda Kegiatan
    </x-slot>

    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Agenda & Jadwal Kegiatan
                </h1>
                <p class="mt-3 text-xl text-gray-500">
                    Ikuti terus perkembangan jadwal pelayanan dan kegiatan jemaat kita.
                </p>
            </div>

            <div class="space-y-6">
                @forelse ($agendas as $agenda)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row overflow-hidden hover:shadow-md transition">
                        <div class="bg-blue-600 text-white flex flex-col justify-center items-center p-6 sm:w-32 flex-shrink-0">
                            <span class="text-3xl font-bold">{{ $agenda->start_time->format('d') }}</span>
                            <span class="text-sm uppercase tracking-wider">{{ $agenda->start_time->format('M Y') }}</span>
                        </div>
                        <div class="p-6 flex-grow">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $agenda->title }}</h2>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $agenda->start_time->format('H:i') }} {{ $agenda->end_time ? '- ' . $agenda->end_time->format('H:i') : 'WIB' }}
                                </div>
                                @if($agenda->location)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $agenda->location }}
                                </div>
                                @endif
                            </div>
                            <div class="text-gray-700 prose prose-sm max-w-none">
                                {!! $agenda->description !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                        Belum ada jadwal kegiatan dalam waktu dekat.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $agendas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>