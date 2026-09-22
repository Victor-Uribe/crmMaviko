<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::post(
    '/login',
    [AuthController::class, 'login']
)->middleware('guest');

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)->middleware('auth');


/*
|--------------------------------------------------------------------------
| Vue SPA
|--------------------------------------------------------------------------
|
| Toda ruta visual de Vue termina en index.html.
|
| NO capturamos:
| /api/*
| /sanctum/*
|
*/

Route::get('/{any}', function () {

    return response()->file(
        public_path('index.html')
    );

})->where(
    'any',
    '^(?!api(?:/|$)|sanctum(?:/|$)).*$'
);
