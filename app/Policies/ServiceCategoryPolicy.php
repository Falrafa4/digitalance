<?php

namespace App\Policies;

use App\Models\ServiceCategory;

class ServiceCategoryPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, ServiceCategory $serviceCategory): bool
    {
        return $this->role($user) === 'administrator' || (bool) $serviceCategory->is_active;
    }

    public function create($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function update($user, ServiceCategory $serviceCategory): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function delete($user, ServiceCategory $serviceCategory): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }
}
