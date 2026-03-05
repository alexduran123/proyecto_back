<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;

Route::get('/', function () {
    return view('welcome');
});
// 2. Ruta para el Dashboard principal
Route::get('/', function () {
    return view('welcome');
});
// 3. Ruta para ver la lista de condominios/unidades
Route::get('/unidades', [PropertyController::class, 'index'])->name('properties.index');

// 4. Ruta para ver el perfil del residente
Route::get('/perfil', function () {
    return view('profile');
})->name('profile');
// Añade esta línea al final de tu archivo web.php
Route::get('/configuracion', function () {
    return view('configuracion');
})->name('settings');
