<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse; // Importamos la respuesta pura de Laravel

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();

        // Si es Técnico, forzamos la redirección nativa al panel del técnico
        if ($user->hasRole('Técnico')) {
            return new RedirectResponse(url('/tecnico'));
        }

        // Si es Admin, lo mandamos a la vista clásica
        if ($user->hasRole('Admin')) {
            return new RedirectResponse(url('/admin'));
        }

        // Fallback por si hay algún usuario sin rol definido
        return new RedirectResponse(url('/admin')); 
    }
}