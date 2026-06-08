<?php

namespace App\Policies;

use App\Models\Negotiation;
use App\Models\Order;

class NegotiationPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Negotiation $negotiation): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $negotiation)
            || $this->belongsToFreelancer($user, $negotiation);
    }

    public function create($user): bool
    {
        return in_array($this->role($user), ['client', 'freelancer'], true);
    }

    public function createForOrder($user, Order $order): bool
    {
        $order->loadMissing('service');

        return $this->belongsToClientOrder($user, $order)
            || ($this->role($user) === 'freelancer'
                && $user->status === 'Approved'
                && $this->belongsToFreelancerOrder($user, $order));
    }

    public function update($user, Negotiation $negotiation): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete($user, Negotiation $negotiation): bool
    {
        return $this->isAdministrator($user);
    }

    public function accept($user, Negotiation $negotiation): bool
    {
        return $this->role($user) === 'freelancer'
            && $user->status === 'Approved'
            && $this->belongsToFreelancer($user, $negotiation);
    }

    public function reject($user, Negotiation $negotiation): bool
    {
        return $this->role($user) === 'freelancer'
            && $user->status === 'Approved'
            && $this->belongsToFreelancer($user, $negotiation);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function belongsToClient($user, Negotiation $negotiation): bool
    {
        $negotiation->loadMissing('order');

        return $this->belongsToClientOrder($user, $negotiation->order);
    }

    private function belongsToFreelancer($user, Negotiation $negotiation): bool
    {
        $negotiation->loadMissing('order.service');

        return $this->belongsToFreelancerOrder($user, $negotiation->order);
    }

    private function belongsToClientOrder($user, ?Order $order): bool
    {
        return $order !== null
            && $this->role($user) === 'client'
            && (int) $order->client_id === (int) $user->id;
    }

    private function belongsToFreelancerOrder($user, ?Order $order): bool
    {
        return $order !== null
            && $this->role($user) === 'freelancer'
            && (int) ($order->freelancer_id ?? $order->service?->freelancer_id) === (int) $user->id;
    }
}
