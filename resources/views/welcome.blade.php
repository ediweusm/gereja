<x-app-layout>
    <x-slot name="title">
        Selamat Datang
    </x-slot>

    <div class="relative h-screen flex items-center justify-center bg-gray-900">
        <!-- <img src="https://images.unsplash.com/photo-1438032005730-c779502fac39?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Background Gereja">
         -->
        @php
            $profile = \App\Models\ChurchProfile::first();
        @endphp

        @if($profile && $profile->hero_image_path)
            <img src="{{ asset('storage/' . $profile->hero_image_path) }}" class="absolute inset-0 w-full h-full object-cover" alt="Background Gereja">
        @else
            <img src="https://images.unsplash.com/photo-1438032005730-c779502fac39?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Background Gereja Default">
        @endif
         <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-6">
                Selamat Datang di <br> <span class="text-blue-400">Jemaat Kami</span>
            </h1>
            <p class="text-lg md:text-2xl text-gray-200 italic mb-10">
                "{{ $profile?->hero_quote ?? 'Menjadi Jemaat yang mandiri dan missioner dengan pelayanan bermutu untuk mewujudkan syalom Allah.' }}"
            </p>
            <a href="#jadwal-ibadah" class="inline-block bg-white text-blue-900 font-bold px-8 py-3 rounded-full hover:bg-blue-50 transition duration-300 shadow-lg">
                Lihat Jadwal Ibadah
            </a>
        </div>
    </div>

    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Pelayan Firman</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="flex flex-wrap justify-center gap-12 text-center">
                @forelse ($pastors as $pastor)
                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                        <div class="w-48 h-48 mx-auto rounded-full overflow-hidden shadow-xl mb-6 border-4 border-gray-50 bg-gray-100 flex items-center justify-center">
                            @if($pastor->image_path)
                                <img src="{{ asset('storage/' . $pastor->image_path) }}" alt="{{ $pastor->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pastor->name) }}&background=0D8ABC&color=fff&size=200" alt="{{ $pastor->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $pastor->name }}</h3>
                        <p class="text-blue-600 font-medium mt-1">{{ $pastor->role }}</p>
                    </div>
                @empty
                    <div class="w-full text-center text-gray-500 italic py-8 border-2 border-dashed border-gray-200 rounded-2xl">
                        Data pelayan firman belum ditambahkan ke dalam sistem.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($sliders->count() > 0)
    <div class="w-full bg-white pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="kegiatan-slider" class="relative w-full aspect-[16/9] md:aspect-[2/1] rounded-3xl overflow-hidden shadow-2xl border border-gray-100 group">
                
                @foreach($sliders as $index => $slider)
                    <div class="absolute inset-0 transition-opacity duration-500 ease-in-out slider-item {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                        <img src="{{ asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title ?? 'Kegiatan Gereja' }}" class="w-full h-full object-cover">
                        
                        @if($slider->title)
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 md:p-8">
                                <h3 class="text-white text-xl md:text-2xl font-bold">{{ $slider->title }}</h3>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slider-item');
            if(slides.length <= 1) return; // Tidak perlu animasi jika gambar hanya 1
            
            let currentSlide = 0;
            
            setInterval(() => {
                // Sembunyikan slide saat ini
                slides[currentSlide].classList.remove('opacity-100', 'z-10');
                slides[currentSlide].classList.add('opacity-0', 'z-0');
                
                // Lanjut ke slide berikutnya
                currentSlide = (currentSlide + 1) % slides.length;
                
                // Tampilkan slide baru
                slides[currentSlide].classList.remove('opacity-0', 'z-0');
                slides[currentSlide].classList.add('opacity-100', 'z-10');
            }, 4000); // Ganti gambar setiap 4 detik
        });
    </script>
    @endif

    <div id="jadwal-ibadah" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-amber-50/80 text-stone-800 p-8 md:p-14 shadow-lg rounded-3xl border border-amber-100">
                
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 text-stone-800">Jadwal Ibadah Minggu Ini</h2>
                    <p class="text-stone-500 font-medium">
                        Periode: {{ now()->startOfWeek()->format('d M Y') }} - {{ now()->endOfWeek()->format('d M Y') }}
                    </p>
                    <div class="w-16 h-1 bg-amber-400 mx-auto mt-6 rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($events as $event)
                        <div class="bg-white p-6 rounded-2xl border border-amber-100 hover:shadow-md hover:-translate-y-1 transition duration-300 flex flex-col h-full shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-stone-800 leading-tight">{{ $event->name }}</h3>
                                    @if($event->mode)
                                        <span class="inline-block bg-amber-100 text-amber-800 px-2 py-1 mt-2 rounded text-[10px] font-bold uppercase tracking-wider">
                                            {{ $event->mode }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-2.5 bg-amber-100 rounded-xl shrink-0 ml-3">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            @if($event->theme)
                                <p class="text-sm font-medium text-stone-500 italic mb-5 flex-grow">"{{ $event->theme }}"</p>
                            @else
                                <div class="flex-grow"></div>
                            @endif
                            
                            <div class="pt-4 border-t border-stone-100 space-y-3 text-sm text-stone-600">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $event->event_date->format('d M Y') }}
                                    </div>
                                    <div class="flex items-center gap-2 font-semibold text-stone-700">
                                        <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} WIB
                                    </div>
                                </div>

                                @if($event->location_notes)
                                    <div class="flex items-start gap-2 pt-2 border-t border-stone-50">
                                        <svg class="w-4 h-4 shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($event->location_notes) }}" target="_blank" class="hover:text-amber-600 hover:underline transition line-clamp-2" title="{{ $event->location_notes }}">
                                            {{ $event->location_notes }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-stone-500 bg-white/60 p-8 rounded-2xl text-center border border-dashed border-stone-300">
                            Belum ada jadwal kegiatan atau ibadah pada minggu ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Berita & Informasi</h2>
                    <div class="w-16 h-1 bg-blue-600 mt-4 rounded-full"></div>
                </div>
                <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 font-medium hidden sm:block">Lihat Semua &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($posts as $post)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition duration-300 flex flex-col">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">Tanpa Gambar</div>
                        @endif
                        <div class="p-6 flex-grow flex flex-col">
                            <div class="text-xs font-bold text-blue-600 uppercase mb-2">{{ $post->category?->name ?? 'Informasi' }}</div>
                            <a href="{{ route('posts.show', $post->slug) }}" class="block mt-1">
                                <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600 leading-tight">{{ $post->title }}</h3>
                            </a>
                            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 mt-auto">
                                {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-2xl">
                        Belum ada berita yang diterbitkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="py-20 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Statistik Pelayanan</h2>
                <p class="text-gray-500 mt-2">Transparansi data jemaat dan pelayanan kasih untuk kemuliaan nama-Nya.</p>
                <div class="w-16 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="p-4 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Jemaat</div>
                        <div class="text-3xl font-bold text-gray-900">{{ number_format($totalMembers, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <div class="text-xs font-semibold text-gray-500 uppercase">Laki-Laki</div>
                            <div class="text-xl font-bold text-blue-600">{{ number_format($maleMembers, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Perempuan</div>
                            <div class="text-xl font-bold text-pink-500">{{ number_format($femaleMembers, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 flex overflow-hidden">
                        <div class="bg-blue-500 h-2" style="width: {{ $totalMembers > 0 ? ($maleMembers / $totalMembers) * 100 : 0 }}%"></div>
                        <div class="bg-pink-400 h-2" style="width: {{ $totalMembers > 0 ? ($femaleMembers / $totalMembers) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="p-4 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Rayon</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalRayons }}</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="p-4 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Diakonia ({{ date('Y') }})</div>
                        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($bantuanYear, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Sebaran Jemaat per Rayon</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($rayons as $rayon)
                        <div class="p-5 border border-gray-100 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-md transition">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-gray-900">{{ $rayon->name }}</h4>
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $rayon->members_count }} Jiwa</span>
                            </div>
                            
                            <div class="flex justify-between text-sm mt-3 pt-3 border-t border-gray-200">
                                <div class="flex items-center gap-1 text-gray-600">
                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    Laki: <strong>{{ $rayon->male_count }}</strong>
                                </div>
                                <div class="flex items-center gap-1 text-gray-600">
                                    <div class="w-2 h-2 rounded-full bg-pink-400"></div>
                                    Prp: <strong>{{ $rayon->female_count }}</strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 italic">
                            Data persebaran jemaat per rayon belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</x-app-layout>