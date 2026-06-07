<?php

namespace App\Policies;

use App\Models\Order;

class OrderPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Order $order): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $order)
            || $this->belongsToFreelancer($user, $order);
    }

    public function create($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client'], true);
    }

    public function update($user, Order $order): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete($user, Order $order): bool
    {
        return $this->isAdministrator($user);
    }

    public function clientAction($user, Order $order): bool
    {
        return $this->belongsToClient($user, $order);
    }

    public function freelancerAction($user, Order $order): bool
    {
        return $this->role($user) === 'freelancer'
            && $user->status === 'Approved'
            && $this->belongsToFreelancer($user, $order);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function belongsToClient($user, Order $order): bool
    {
        return $this->role($user) === 'client' && (int) $order->client_id === (int) $user->id;
    }

    private function belongsToFreelancer($user, Order $order): bool
    {
        $order->loadMissing('service');

        return $this->role($user) === 'freelancer'
            && (int) ($order->freelancer_id ?? $order->service?->freelancer_id) === (int) $user->id;
    }
}
