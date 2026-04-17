<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView; // Importante para los Hooks
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->topNavigation()
            ->login(\App\Filament\Pages\Auth\UnifiedLogin::class)
            ->registration(\App\Filament\Pages\Auth\RegistroTecnico::class)
            ->passwordReset()
            ->homeUrl(fn () => auth()->user()?->hasRole('Director') ? '/admin/expedientes' : '/admin')

            // --- PERSONALIZACIÓN VISUAL ---
            //->brandName('Gestión CFI')
            ->brandLogo(asset('images/logo-cfi.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->darkMode(false)
            ->colors([
                'primary' => Color::hex('#0055A5'),
            ])

            // --- CONFIGURACIÓN DE NAVEGACIÓN ---
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()->label('Salir'),
            ])

            // --- HOOKS PARA EL DIRECTOR (Interfaz Limpia) ---
            ->renderHook(
                'panels::head.end',
                fn () => new HtmlString("
                    <style>
                        /* 1. Estilos para el Director (Lo que ya tenías) */
                        " . (auth()->check() && auth()->user()->hasRole('Director') ? "
                            .fi-sidebar, .fi-topbar-start button { display: none !important; }
                            .fi-main-ctn { margin-left: 0 !important; }
                            .fi-topbar nav-list { 
                                display: flex !important; 
                                justify-content: flex-start !important; 
                                gap: 2rem !important; 
                                width: 100% !important; 
                            }
                            .fi-topbar-start button { display: none !important; }
                            .fi-topbar { background-color: white !important; border-bottom: 1px solid #e5e7eb !important; }
                        " : "") . "

                        /* 2. SOLUCIÓN AL PAGINADOR (Para todos los roles) */

                        /* Forzamos que el pie de la tabla no colapse el contenido */
                        .fi-ta-pagination nav {
                            display: flex !important;
                            width: 100% !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                        }

                        /* Forzamos visibilidad del texto 'Se muestran de X a Y...' */
                        .fi-ta-pagination-records-range-label {
                            display: block !important;
                        }

                        /* Forzamos visibilidad de la lista de números (1, 2, 3...) */
                        .fi-ta-pagination-list {
                            display: flex !important;
                        }

                        /* Evitamos que los botones de Siguiente/Anterior tapen el resto en móviles */
                        .fi-ta-pagination div:last-child {
                            display: flex !important;
                            gap: 0.5rem;
                        }
                    </style>
                ")
            )

            // 2. EL HOOK DEL LOGO (Para que aparezca a la izquierda en ese espacio)
            ->renderHook(
                'panels::topbar.start',
                fn () => auth()->check() && auth()->user()->hasRole('Director')
                    ? new HtmlString("
                        <div class='flex items-center px-4'>
                            <img src='" . asset('images/logo-cfi.png') . "' alt='Logo CFI' style='height: 2.5rem; width: auto;'>
                        </div>
                    ") : ''
            )
            // --- DESCUBRIMIENTO AUTOMÁTICO ---
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
