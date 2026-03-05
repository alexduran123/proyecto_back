<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
// FALTA ESTA LÍNEA:
use App\Http\Controllers\ChatController; 

// Esta ruta servirá para que React pida los datos del dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// Esta es la ruta para el Chat
Route::post('/send-message', [ChatController::class, 'sendMessage']);

Route::get('/messages', function () {
    // Traemos los últimos 50 mensajes de la tabla 'mensajes'
    return App\Models\Mensaje::latest()->take(50)->get()->reverse()->values();
});