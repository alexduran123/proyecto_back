<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_persona' => 'required|integer',
            'email'      => 'required|email|unique:usuarios,email',
            'pass'       => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // 1. Creamos el usuario
            $user = Usuario::create([
                'id_persona' => $request->id_persona,
                'email'      => $request->email,
                'pass'       => Hash::make($request->pass),
                'admin'      => false,
            ]);

            // 2. Intentamos enviar el correo de verificación
            try {
                event(new Registered($user));
                $mailStatus = "Correo de verificación enviado a Gmail.";
            } catch (\Exception $e) {
                Log::error("Error SMTP al enviar correo: " . $e->getMessage());
                $mailStatus = "Usuario creado, pero no se pudo enviar el correo de verificación. Revisa la configuración SMTP.";
            }

            return response()->json([
                'message' => 'Usuario registrado correctamente.',
                'mail_status' => $mailStatus,
                'user_id' => $user->id_usuario // o el nombre de tu PK
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error en el registro: " . $e->getMessage());
            return response()->json([
                'message' => 'Error interno al procesar el registro.'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validamos que lleguen los datos
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required'
        ]);

        // Mapeamos 'pass' al campo 'password' que Laravel espera internamente
        $credentials = [
            'email'    => $request->email,
            'password' => $request->pass 
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Las credenciales no coinciden con nuestros registros.'
            ], 401);
        }

        $user = Auth::user();

        // 3. Verificamos si el correo ha sido verificado (campo email_verified_at)
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico.'
            ], 403);
        }

        // Generamos el token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'id'    => $user->id_usuario, // Ajusta según tu PK
                'email' => $user->email,
                'role'  => $user->admin ? 'admin' : 'residente'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}