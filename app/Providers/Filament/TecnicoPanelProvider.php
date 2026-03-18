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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\MaxWidth;

class TecnicoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            ->id('tecnico')
            ->path('tecnico') // El técnico va a entrar por misistema.com/tecnico
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Blue, // Podés ponerle otro color para que se den cuenta rápido dónde están
            ])
            // LA MAGIA DE LA VISTA ACÁ:
            ->topNavigation() 
            ->maxContentWidth(MaxWidth::Full) 

            // LA MAGIA DE RECICLAR CÓDIGO:
            // Le decimos que lea EXACTAMENTE la misma carpeta de recursos que el panel Admin.
            // Así, el ExpedienteResource que ya programamos sirve para los dos paneles.
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            
            // Si querés que el técnico no vea el "Escritorio" y vaya directo a Expedientes, 
            // comentá o borrá esta línea de abajo:
            // ->pages([Pages\Dashboard::class]) 

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
