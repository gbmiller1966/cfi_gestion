<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): \Symfony\Component\HttpFoundation\Response
    {
        // Sin importar de qué panel vengan, los mandamos siempre al login principal
        return redirect()->to('/admin/login');
    }
}
