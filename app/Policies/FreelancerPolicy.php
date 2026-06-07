<?php

namespace App\Policies;

use App\Models\Freelancer;

class FreelancerPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Freelancer $freelancer): bool
    {
        return $this->role($user) === 'administrator'
            || ($this->role($user) === 'freelancer' && (int) $user->id === (int) $freelancer->id)
            || $freelancer->status === 'Approved';
    }

    public function create($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function update($user, Freelancer $freelancer): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function delete($user, Freelancer $freelancer): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function moderate($user, Freelancer $freelancer): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }
}
