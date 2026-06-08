<?php

namespace App\Policies;

use App\Models\Loker;

class LokerPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Loker $loker): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($this->role($user) === 'client') {
            return (int) $loker->client_id === (int) $user->id;
        }

        if ($this->role($user) === 'freelancer') {
            return $loker->status === 'Open'
                || $loker->applications()->where('freelancer_id', $user->id)->exists();
        }

        return false;
    }

    public function create($user): bool
    {
        return $this->role($user) === 'client';
    }

    public function update($user, Loker $loker): bool
    {
        return $this->isAdministrator($user)
            || ($this->role($user) === 'client' && (int) $loker->client_id === (int) $user->id);
    }

    public function delete($user, Loker $loker): bool
    {
        return $this->isAdministrator($user)
            || ($this->role($user) === 'client' && (int) $loker->client_id === (int) $user->id);
    }

    public function apply($user, Loker $loker): bool
    {
        return $this->role($user) === 'freelancer';
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }
}
