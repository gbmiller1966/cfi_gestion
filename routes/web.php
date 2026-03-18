<?php

use Illuminate\Support\Facades\Route;

/* Route::get('/', function () {
    return view('welcome');
}); */

// Redirige una URL limpia al login del panel principal
Route::redirect('/login', '/admin/login')->name('login');

// Opcional: Si entran a la raíz del sitio, también los mandamos al login
Route::redirect('/', '/admin/login');