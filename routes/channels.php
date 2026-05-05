<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('negotiation.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (!$order) return false;

    // client boleh
    if (auth('client')->check() && $order->client_id === auth('client')->id()) {
        return true;
    }

    // freelancer boleh
    if (auth('freelancer')->check() && $order->service->freelancer_id === auth('freelancer')->id()) {
        return true;
    }

    return false;
});
