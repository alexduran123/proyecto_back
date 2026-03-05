<?php

namespace App\Http\Controllers;

use App\Events\MessageSent; // <--- Verifica que esta línea esté
use Illuminate\Http\Request;
use App\Models\Mensaje;
class ChatController extends Controller
{
    public function sendMessage(Request $request)
{
    $request->validate(['message' => 'required|string']);

    // Guardamos en la tabla 'mensajes'
    $nuevoMensaje = Mensaje::create([
        'remitente'    => 1, // ID de una persona que ya exista en tu tabla 'personas'
        'destinatario' => 2, // ID de otra persona que ya exista
        'mensaje'      => $request->message,
        'fecha'        => now(),
    ]);

    // Enviamos el texto a través de Reverb (puerto 8080)
    broadcast(new \App\Events\MessageSent($nuevoMensaje->mensaje))->toOthers();

    return response()->json(['status' => 'Guardado en DB y enviado']);
}
}