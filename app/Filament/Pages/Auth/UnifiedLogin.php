<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;

class UnifiedLogin extends BaseLogin
{
    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        $user = Filament::auth()->user();

        // Usamos la redirección interna de Livewire. Chau 419 y chau errores raros.
        if ($user->hasAnyRole(['Técnico', 'Tecnico', 'tecnico'])) {
            $this->redirect(url('/tecnico'));
            return null; // Retornamos null porque el redirect ya corta la ejecución
        }

        $this->redirect(url('/admin'));
        return null;
    }
}
