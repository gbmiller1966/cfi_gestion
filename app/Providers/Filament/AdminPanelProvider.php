<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
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
            ->homeUrl(function () {
                $user = auth()->user();
                if (!$user) return '/admin/login';

                // Si tiene rol de Director o Jefe de Área, y tiene sus campos asignados, va a expedientes
                if ($user->hasAnyRole(['Director', 'Jefe de Área'])) {
                    return ($user->direccion_id || $user->area_id) ? '/admin/expedientes' : '/admin';
                }

                return '/admin';
            })

            // --- PERSONALIZACIÓN VISUAL ---
            ->brandLogo(asset('images/logo-cfi.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.png'))
            ->darkMode(false)
            ->colors([
                'primary' => Color::hex('#0055A5'),
            ])

            // --- CONFIGURACIÓN DE NAVEGACIÓN ---
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()->label('Salir'),
            ])

            // --- HOOKS DE ESTILOS ---
            ->renderHook(
                'panels::head.end',
                fn () => auth()->check() ? new HtmlString("
                    <style>
                        /* 1. Forzar que la navegación superior parezca un menú de pestañas */
                        .fi-topbar-nav-list {
                            display: flex !important;
                            gap: 1.5rem !important;
                            margin-left: 2rem !important;
                        }

                        /* 2. Estilo de los links de navegación (opcional, para resaltar la activa) */
                        .fi-topbar-nav-link-active {
                            border-bottom: 2px solid #0055A5 !important;
                            border-radius: 0 !important;
                        }

                        /* 3. Limpieza de elementos innecesarios en modo TopNav */
                        .fi-topbar-start button[aria-controls='sidebar'] {
                            display: none !important;
                        }

                        /* 4. Estilos del Paginador Miller */
                        .fi-ta-pagination nav {
                            display: flex !important;
                            width: 100% !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                        }
                        .fi-ta-pagination-records-range-label, .fi-ta-pagination-list {
                            display: flex !important;
                            visibility: visible !important;
                        }
                        .fi-ta-pagination-simple { display: none !important; }
                    </style>
                ") : ""
            )

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
