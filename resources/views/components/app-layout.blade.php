@php
    // Memanggil baris pertama dari tabel ChurchProfile
    $profile = \App\Models\ChurchProfile::first();
    
    // Memberikan nilai *fallback* (cadangan) jika tabel masih kosong
    $churchName = $profile->church_name ?? 'Nama Gereja';
    $logoPath = $profile->logo_path ?? null;
    $address = $profile->address ?? 'Alamat gereja belum diatur.';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $title ?? 'Beranda' }} - {{ $churchName }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <header class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100/50 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="/" class="flex items-center gap-2 font-bold text-xl text-blue-800">
                            @if($logoPath)
                                <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-10 w-auto object-contain">
                            @endif
                            <span>{{ $churchName }}</span>
                        </a>
                    </div>
                    <div class="hidden sm:-my-px sm:ml-8 sm:flex sm:space-x-8">
                        <a href="/" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('/') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }} text-sm font-medium">Beranda</a>
                        <a href="{{ route('agendas.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('agendas.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }} text-sm font-medium">Agenda</a>
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('posts.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }} text-sm font-medium">Berita</a>
                        <a href="{{ route('galleries.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('galleries.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }} text-sm font-medium">Galeri</a>
                        <a href="{{ route('sermons.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('sermons.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300' }} text-sm font-medium">Khotbah</a>
                    </div>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <a href="/admin" class="text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 px-5 py-2 rounded-full transition shadow-md">Masuk</a>
                </div>
            </div>
        </div>
    </header>

    <main class="{{ request()->is('/') ? '' : 'pt-16' }} min-h-screen">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <p class="text-center text-sm text-gray-600 mb-2">
                {{ $address }}
            </p>
            <p class="text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ $churchName }}. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>