<?php

namespace App\Policies;

use App\Models\Transaction;

class TransactionPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Transaction $transaction): bool
    {
        return $this->isAdministrator($user)
            || $this->belongsToClient($user, $transaction)
            || $this->belongsToFreelancer($user, $transaction);
    }

    public function create($user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update($user, Transaction $transaction): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete($user, Transaction $transaction): bool
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

    private function belongsToClient($user, Transaction $transaction): bool
    {
        $transaction->loadMissing('order');

        return $this->role($user) === 'client'
            && (int) $transaction->order?->client_id === (int) $user->id;
    }

    private function belongsToFreelancer($user, Transaction $transaction): bool
    {
        $transaction->loadMissing('order.service');

        return $this->role($user) === 'freelancer'
            && (int) ($transaction->order?->freelancer_id ?? $transaction->order?->service?->freelancer_id) === (int) $user->id;
    }
}
