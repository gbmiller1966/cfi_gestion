<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // 1. Redirigir al Director apenas intente entrar
    public function mount(): void
    {
        if (auth()->user()?->hasRole('Director')) {
            redirect('/admin/expedientes');
        }
    }

    // 2. Ocultar el "Escritorio" del menú lateral solo para el Director
    public static function shouldPageBeVisible(): bool
    {
        return ! auth()->user()?->hasRole('Director');
    }
}
