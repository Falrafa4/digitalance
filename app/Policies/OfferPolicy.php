<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\Order;

class OfferPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Offer $offer): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $offer)
            || $this->belongsToFreelancer($user, $offer);
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

    public function update($user, Offer $offer): bool
    {
        return $this->belongsToFreelancer($user, $offer)
            && $user->status === 'Approved';
    }

    public function delete($user, Offer $offer): bool
    {
        return $this->isAdministrator($user);
    }

    public function accept($user, Offer $offer): bool
    {
        return $this->belongsToClient($user, $offer);
    }

    public function reject($user, Offer $offer): bool
    {
        return $this->belongsToClient($user, $offer);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function belongsToClient($user, Offer $offer): bool
    {
        $offer->loadMissing('order');

        return $this->role($user) === 'client'
            && (int) $offer->order?->client_id === (int) $user->id;
    }

    private function belongsToFreelancer($user, Offer $offer): bool
    {
        $offer->loadMissing('order.service');

        return $this->role($user) === 'freelancer'
            && (int) ($offer->order?->freelancer_id ?? $offer->order?->service?->freelancer_id) === (int) $user->id;
    }
}
