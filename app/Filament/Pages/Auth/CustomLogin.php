<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Login';
    }
}
