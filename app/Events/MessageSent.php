<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Cambia ShouldBroadcast por ShouldBroadcastNow
class MessageSent implements \Illuminate\Contracts\Broadcasting\ShouldBroadcastNow 
{
    use \Illuminate\Foundation\Events\Dispatchable, \Illuminate\Broadcasting\InteractsWithSockets, \Illuminate\Queue\SerializesModels;

    public $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function broadcastOn(): array {
        return [new \Illuminate\Broadcasting\Channel('chat')];
    }
}
