<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Envía el código de 6 dígitos al correo
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = Usuario::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'El correo no existe'], 404);
        }

        $code = rand(100000, 999999);

        // Limpio códigos anteriores y guardo el nuevo
        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'created_at' => now()
        ]);

        // Envío por Gmail
        try {
            Mail::raw("Tu código de recuperación es: $code", function($message) use ($user) {
                $message->to($user->email)->subject('Código de Recuperación - CondoApp');
            });
            return response()->json(['message' => 'Código enviado']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar mail'], 500);
        }
    }

    // Valida el código y cambia la contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        // El código dura 15 minutos
        if (!$record || Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'Código inválido o vencido'], 422);
        }

        $user = Usuario::where('email', $request->email)->first();
        $user->pass = Hash::make($request->password);
        $user->save();

        // Borro el código y cierro sesiones en otros lados
        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        $user->tokens()->delete();

        return response()->json(['message' => 'Contraseña actualizada con éxito']);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->pass
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Datos incorrectos'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Falta verificar correo'], 403);
        }

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

        if (!Hash::check($request->current_password, $user->pass)) {
            return response()->json(['message' => 'Clave actual incorrecta'], 422);
        }

        $user->pass = Hash::make($request->new_password);
        $user->save();

        $user->tokens()->delete();

        return response()->json(['message' => 'Clave cambiada y sesiones cerradas']);
    }
}