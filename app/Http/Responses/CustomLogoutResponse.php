<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as ResponsiveContract;
use Illuminate\Http\RedirectResponse;

class CustomLogoutResponse implements ResponsiveContract
{
    public function toResponse($request): RedirectResponse
    {
        // Mengalihkan pengguna langsung ke halaman utama (/) setelah logout
        return redirect('/');
    }
}