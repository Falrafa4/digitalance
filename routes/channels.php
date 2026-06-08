<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('negotiation.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (! $order) {
        return false;
    }

    // client boleh
    if (auth('client')->check() && $order->client_id === auth('client')->id()) {
        return true;
    }

    // freelancer boleh
    if (auth('freelancer')->check() && optional($order->service)->freelancer_id === auth('freelancer')->id()) {
        return true;
    }

    return false;
}, ['guards' => ['client', 'freelancer']]);

Broadcast::channel('notifications.{role}.{userId}', function ($user, $role, $userId) {
    // Administrator guard
    if (auth('administrator')->check()) {
        $authUser = auth('administrator')->user();
        return $role === 'admin' && (int) $authUser->id === (int) $userId;
    }
    // Client guard
    if (auth('client')->check()) {
        $authUser = auth('client')->user();
        return $role === 'client' && (int) $authUser->id === (int) $userId;
    }
    // Freelancer guard
    if (auth('freelancer')->check()) {
        $authUser = auth('freelancer')->user();
        return $role === 'freelancer' && (int) $authUser->id === (int) $userId;
    }
    return false;
}, ['guards' => ['administrator', 'client', 'freelancer']]);
