<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Events\NotificationSent;
use App\Http\Controllers\ChatController; 
use App\Http\Controllers\AuthController;
use App\Models\Mensaje;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (Requieren Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Chat
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages', function () {
        return Mensaje::latest()->take(50)->get()->reverse()->values();
    });

    // Envío de Notificaciones (Solo si es Admin)
    Route::post('/send-notif', function (Request $request) {
    // Quitamos el IF un momento para probar si es un problema de permisos
    $type = $request->input('type');
    $message = $request->input('message');
    $id_referencia = $request->input('id_referencia', 0);

    event(new NotificationSent($type, $message, $id_referencia));

    return response()->json([
        'status' => 'success',
        'message' => 'Notificación enviada correctamente'
    ]);
});
});