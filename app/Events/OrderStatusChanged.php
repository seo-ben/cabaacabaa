<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Commande $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a public channel identified by order number for tracking
        // Or private channel if they are logged in. Let's use public for the track page.
        return [
            new Channel('order.' . $this->order->numero_commande),
        ];
    }

    public function broadcastAs()
    {
        return 'order.status.changed';
    }

    public function broadcastWith()
    {
        return [
            'statut' => $this->order->statut,
            'label' => ucfirst(str_replace('_', ' ', $this->order->statut))
        ];
    }
}
