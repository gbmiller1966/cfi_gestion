<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirige URL limpia al login
Route::redirect('/login', '/admin/login')->name('login');

// Modificamos la raíz para que sea inteligente:
Route::get('/', function () {
    $user = Auth::user();

    // Si no está logueado, al login
    if (!$user) {
        return redirect('/admin/login');
    }

    // Si es Director o Jefe de Área, directo a su herramienta de trabajo
    if ($user->hasRole(['Director', 'Jefe de Área'])) {
        return redirect('/admin/expedientes');
    }

    // Si es Técnico, al panel de técnicos
    if ($user->hasRole('Técnico')) {
        return redirect('/tecnico');
    }

    // Si es Admin, al dashboard principal
    return redirect('/admin');
});
