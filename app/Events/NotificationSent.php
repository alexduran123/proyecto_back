<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <--- IMPORTANTE
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Agregamos "implements ShouldBroadcast" aquí abajo:
class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Declaramos la variable como pública para que viaje en el WebSocket
    public $data;

    public function __construct($type, $message, $id_referencia)
    {
        $this->data = [
            'type' => $type,
            'message' => $message,
            'id_referencia' => $id_referencia,
        ];
    }

    public function broadcastOn()
    {
        // Canal público para que todos los vecinos reciban alertas
        return new Channel('condo-notifications');
    }

    // OPCIONAL: Esto ayuda a que React reciba el nombre del evento más limpio
    public function broadcastAs()
    {
        return 'notificacion.nueva';
    }
}