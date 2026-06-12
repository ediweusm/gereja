<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('auto')
            ->favicon(function () {
                $profile = \App\Models\ChurchProfile::first();
                return $profile && $profile->logo_path 
                    ? asset('storage/' . $profile->logo_path) 
                    : asset('favicon.ico');
            })
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => new \Illuminate\Support\HtmlString('
                    <style>
                        /* Light Mode: Sidebar Biru */
                        .fi-sidebar {
                            background-color: #739ec9 !important;
                            border-right: 1px solid #cbd5e1 !important;
                        }
                        /* Light Mode: Teks & Ikon Hitam */
                        html:not(.dark) .fi-sidebar,
                        html:not(.dark) .fi-sidebar a,
                        html:not(.dark) .fi-sidebar span,
                        html:not(.dark) .fi-sidebar div,
                        html:not(.dark) .fi-sidebar button,
                        html:not(.dark) .fi-sidebar svg {
                            color: #000000 !important;
                        }
                        html:not(.dark) .fi-sidebar .fi-sidebar-item-button:hover {
                            background-color: rgba(255, 255, 255, 0.2) !important;
                        }
                        html:not(.dark) .fi-sidebar .fi-sidebar-item-active {
                            background-color: rgba(255, 255, 255, 0.35) !important;
                        }

                        /* Dark Mode: Sidebar Dark Navy/Slate */
                        .dark .fi-sidebar {
                            background-color: #313647 !important;
                            border-right: 1px solid #2d264b !important;
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->plugin(FilamentShieldPlugin::make())
            ->navigationGroups([
                'Manajemen Konten',
                'Administrasi Jemaat',
                'Manajemen Keuangan',
                'Pengaturan dan Master Data',
                'Akses dan Keamanan',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
