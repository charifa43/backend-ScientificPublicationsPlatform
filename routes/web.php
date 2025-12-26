<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes pour servir l'application React SPA
| Le backend Laravel sert uniquement l'API (routes/api.php)
| L'authentification se fait via Sanctum tokens
|
*/

// Route principale qui sert votre application React
Route::get('/', function () {
    return view('app');
})->name('home');

// Route fallback pour toutes les routes React (SPA)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');



