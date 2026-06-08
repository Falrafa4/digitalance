<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Result;

class ResultPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Result $result): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $result)
            || $this->belongsToFreelancer($user, $result);
    }

    public function create($user): bool
    {
        return $this->role($user) === 'freelancer'
            && $user->status === 'Approved';
    }

    public function createForOrder($user, Order $order): bool
    {
        $order->loadMissing('service');

        return $this->create($user)
            && (int) ($order->freelancer_id ?? $order->service?->freelancer_id) === (int) $user->id;
    }

    public function update($user, Result $result): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete($user, Result $result): bool
    {
        return $this->isAdministrator($user);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function belongsToClient($user, Result $result): bool
    {
        $result->loadMissing('order');

        return $this->role($user) === 'client'
            && (int) $result->order?->client_id === (int) $user->id;
    }

    private function belongsToFreelancer($user, Result $result): bool
    {
        $result->loadMissing('order.service');

        return $this->role($user) === 'freelancer'
            && (int) ($result->order?->freelancer_id ?? $result->order?->service?->freelancer_id) === (int) $user->id;
    }
}
