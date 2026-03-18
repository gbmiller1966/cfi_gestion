<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse; // <-- Esto es lo que pide Filament

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();

        if ($user->hasRole('Técnico')) {
            return new RedirectResponse(url('/tecnico'));
        }
        // Si es Admin, forzamos la respuesta HTTP pura al principal
        return new RedirectResponse(url('/admin'));
    }
}
