<?php

namespace App\Events;

use App\Models\Negotiation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NegotiationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public Negotiation $negotiation;

    /**
     * Create a new event instance.
     */
    public function __construct(Negotiation $negotiation)
    {
        $this->negotiation = $negotiation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('negotiation.' . $this->negotiation->order_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->negotiation->id,
            'order_id' => $this->negotiation->order_id,
            'sender' => $this->negotiation->sender,
            'message' => $this->negotiation->message,
            'created_at' => optional($this->negotiation->created_at)->toISOString(),
        ];
    }
}
