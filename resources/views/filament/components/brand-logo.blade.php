@php
    $profile = \App\Models\ChurchProfile::first();
    // Deteksi jika user berada di route auth (halaman login) atau belum login
    $isLogin = request()->routeIs('filament.*.auth.login') || !auth()->check();
@endphp

@if($isLogin)
    {{-- TAMPILAN HALAMAN LOGIN: Vertikal (Logo di atas, Nama Gereja di bawah) --}}
    <div class="flex flex-col items-center justify-center w-full">
        @if($profile && $profile->logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" class="h-16 w-auto mb-2">
        @else
            <svg class="w-12 h-12 text-primary-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l9-5-9-5-9 5 9 5z" />
            </svg>
        @endif
        
        <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white text-center">
            {{ $profile ? $profile->church_name : 'Kolhua Kaesarea' }}
        </span>
    </div>
@else
    {{-- TAMPILAN HEADER PANEL: Horizontal/Kecil (HANYA Logo) Dibuat Center Paksa --}}
    <div class="flex items-center w-full">
        @if($profile && $profile->logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->logo_path) }}" alt="Logo" class="h-10 w-auto" style="margin: 0 auto;">
        @else
            <svg class="w-8 h-8 text-primary-600" style="margin: 0 auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l9-5-9-5-9 5 9 5z" />
            </svg>
        @endif
    </div>
@endif