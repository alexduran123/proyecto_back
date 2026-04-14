<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Events\NotificationSent;
use App\Http\Controllers\ChatController; 
use App\Http\Controllers\AuthController;
use App\Models\Mensaje;
use App\Models\Usuario;


//Rutas Públicas


// Registro y Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Ruta de Verificación de Email
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    // Buscamos al usuario por su ID
    $user = Usuario::findOrFail($id);

    // Verificamos que el hash del enlace coincida con el correo del usuario
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'El enlace de verificación ha expirado o es inválido.'], 403);
    }

    // Si no está verificado, lo marcamos ahora
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        // Disparar evento opcional si quieres hacer algo extra al verificar
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    // Redirigir al Login de React con un parámetro de éxito
    return redirect('http://localhost:5173/login?verified=true');
})->name('verification.verify');


//Rutas Protegidas (Requieren Login y Email Verificado)


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Perfil de usuario y Dashboard
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Chat en tiempo real
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages', function () {
        return Mensaje::latest()->take(50)->get()->reverse()->values();
    });

    // Notificaciones (Solo Administradores)
    Route::post('/send-notif', function (Request $request) {
        // Validación de rol administrativo
        if (!$request->user()->admin) {
            return response()->json(['message' => 'Acceso denegado: Se requieren permisos de administrador.'], 403);
        }

        $type = $request->input('type');
        $message = $request->input('message');
        $id_referencia = $request->input('id_referencia', 0);

        // Disparar evento para Pusher/Websockets
        event(new NotificationSent($type, $message, $id_referencia));

        return response()->json([
            'status' => 'success',
            'message' => 'Notificación enviada globalmente'
        ]);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});