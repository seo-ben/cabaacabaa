<?php

namespace App\Events;

use App\Models\OrderMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(OrderMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $order = $this->message->commande;
        // Same channel as tracking/order status for simplicity
        return [
            new Channel('order.' . $order->numero_commande),
        ];
    }

    public function broadcastAs()
    {
        return 'message.new';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'id_commande' => $this->message->id_commande,
            'id_user' => $this->message->id_user,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toISOString(),
            'user' => $this->message->user ? [
                'id_user' => $this->message->user->id_user,
                'name' => $this->message->user->name,
                'short_name' => $this->message->user->name,
                'photo_profil' => $this->message->user->photo_profil
            ] : null
        ];
    }
}
