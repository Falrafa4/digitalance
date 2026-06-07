<?php

namespace App\Policies;

use App\Models\Service;

class ServicePolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Service $service): bool
    {
        return $this->isAdministrator($user)
            || $this->ownsService($user, $service)
            || $this->isPubliclyVisible($service);
    }

    public function create($user): bool
    {
        return $this->role($user) === 'freelancer' && $user->status === 'Approved';
    }

    public function update($user, Service $service): bool
    {
        return $this->isAdministrator($user)
            || ($this->role($user) === 'freelancer' && $user->status === 'Approved' && $this->ownsService($user, $service));
    }

    public function delete($user, Service $service): bool
    {
        return $this->update($user, $service);
    }

    public function submit($user, Service $service): bool
    {
        return $this->role($user) === 'freelancer'
            && $user->status === 'Approved'
            && $this->ownsService($user, $service);
    }

    public function updateStatus($user, Service $service): bool
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

    private function ownsService($user, Service $service): bool
    {
        return $this->role($user) === 'freelancer' && (int) $service->freelancer_id === (int) $user->id;
    }

    private function isPubliclyVisible(Service $service): bool
    {
        $service->loadMissing('freelancer');

        return $service->status === 'Approved'
            && $service->freelancer_id
            && $service->freelancer
            && $service->freelancer->status === 'Approved';
    }
}
