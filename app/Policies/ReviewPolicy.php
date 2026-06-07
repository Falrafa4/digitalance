<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Review;

class ReviewPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Review $review): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $review)
            || $this->belongsToFreelancer($user, $review);
    }

    public function create($user): bool
    {
        return $this->role($user) === 'client';
    }

    public function createForOrder($user, Order $order): bool
    {
        return $this->role($user) === 'client'
            && (int) $order->client_id === (int) $user->id;
    }

    public function update($user, Review $review): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete($user, Review $review): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $review);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function belongsToClient($user, Review $review): bool
    {
        $review->loadMissing('order');

        return $this->role($user) === 'client'
            && (int) $review->order?->client_id === (int) $user->id;
    }

    private function belongsToFreelancer($user, Review $review): bool
    {
        $review->loadMissing('order.service');

        return $this->role($user) === 'freelancer'
            && (int) ($review->order?->freelancer_id ?? $review->order?->service?->freelancer_id) === (int) $user->id;
    }
}
