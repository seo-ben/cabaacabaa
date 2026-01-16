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

class OrderPlaced implements ShouldBroadcast
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
        return [
            new PrivateChannel('vendeur.' . $this->order->id_vendeur),
        ];
    }

    public function broadcastAs()
    {
        return 'order.placed';
    }

    public function broadcastWith()
    {
        return [
            'order' => [
                'id' => $this->order->id_commande,
                'numero' => $this->order->numero_commande,
                'total' => $this->order->montant_total,
                'client' => $this->order->nom_complet_client,
                'statut' => $this->order->statut,
            ]
        ];
    }
}
