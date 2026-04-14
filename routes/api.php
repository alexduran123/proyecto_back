<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Events\NotificationSent;
use App\Http\Controllers\ChatController; 
use App\Http\Controllers\AuthController;
use App\Models\Mensaje;
use App\Models\Usuario;

// Rutas Públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Verificación de cuenta desde el correo
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = Usuario::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Link inválido'], 403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect('http://localhost:5173/login?verified=true');
})->name('verification.verify');

// Rutas Públicas
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Rutas Protegidas
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Perfil y Dashboard
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Seguridad: Cambio de clave y logout
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Chat
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages', function () {
        return Mensaje::latest()->take(50)->get()->reverse()->values();
    });

    // Notificaciones de Admin
    Route::post('/send-notif', function (Request $request) {
        if (!$request->user()->admin) {
            return response()->json(['message' => 'No eres admin'], 403);
        }

        $type = $request->input('type');
        $message = $request->input('message');
        $id_referencia = $request->input('id_referencia', 0);

        event(new NotificationSent($type, $message, $id_referencia));

        return response()->json([
            'status' => 'success',
            'message' => 'Notificación enviada'
        ]);
    });
});