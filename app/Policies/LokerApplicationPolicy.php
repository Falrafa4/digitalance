<?php

namespace App\Policies;

use App\Models\LokerApplication;

class LokerApplicationPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, LokerApplication $application): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        $application->loadMissing('loker');

        return ($this->role($user) === 'client' && (int) $application->loker?->client_id === (int) $user->id)
            || ($this->role($user) === 'freelancer' && (int) $application->freelancer_id === (int) $user->id);
    }

    public function approve($user, LokerApplication $application): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        $application->loadMissing('loker');

        return $this->role($user) === 'client'
            && (int) $application->loker?->client_id === (int) $user->id;
    }

    public function reject($user, LokerApplication $application): bool
    {
        return $this->approve($user, $application);
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
