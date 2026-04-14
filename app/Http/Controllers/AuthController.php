<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->pass
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Verifica tu correo primero'], 403);
        }

        // Genera un token nuevo para este dispositivo
        $token = $user->createToken('dispositivo_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'id'    => $user->id_usuario,
                'email' => $user->email,
                'role'  => $user->admin ? 'admin' : 'residente'
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Valido que la vieja sea la correcta
        if (!Hash::check($request->current_password, $user->pass)) {
            return response()->json(['message' => 'Contraseña actual mal'], 422);
        }

        // Guardo la nueva
        $user->pass = Hash::make($request->new_password);
        $user->save();

        // Borro todos los tokens para sacar a todos los dispositivos
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Contraseña cambiada. Sesiones cerradas en todo lado.'
        ]);
    }
}