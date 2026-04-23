<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NegotiationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $negotiation;

    /**
     * Create a new event instance.
     */
    public function __construct($negotiation)
    {
        $this->negotiation = $negotiation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('negotiation.' . $this->negotiation->order_id);
    }
}
