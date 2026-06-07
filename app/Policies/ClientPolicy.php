<?php

namespace App\Policies;

use App\Models\Client;

class ClientPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'freelancer'], true);
    }

    public function view($user, Client $client): bool
    {
        return $this->role($user) === 'administrator'
            || ($this->role($user) === 'client' && (int) $user->id === (int) $client->id);
    }

    public function create($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function update($user, Client $client): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function delete($user, Client $client): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }
}
