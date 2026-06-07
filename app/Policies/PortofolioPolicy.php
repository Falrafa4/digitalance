<?php

namespace App\Policies;

use App\Models\Portofolio;

class PortofolioPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'client', 'freelancer'], true);
    }

    public function view($user, Portofolio $portofolio): bool
    {
        return $this->isAdministrator($user)
            || $this->ownsPortofolio($user, $portofolio)
            || $this->isPubliclyVisible($portofolio);
    }

    public function create($user): bool
    {
        return $this->role($user) === 'freelancer' && $user->status === 'Approved';
    }

    public function update($user, Portofolio $portofolio): bool
    {
        return $this->isAdministrator($user)
            || ($this->role($user) === 'freelancer' && $user->status === 'Approved' && $this->ownsPortofolio($user, $portofolio));
    }

    public function delete($user, Portofolio $portofolio): bool
    {
        return $this->update($user, $portofolio);
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }

    private function isAdministrator($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function ownsPortofolio($user, Portofolio $portofolio): bool
    {
        $portofolio->loadMissing('service');

        return $this->role($user) === 'freelancer'
            && (int) $portofolio->service?->freelancer_id === (int) $user->id;
    }

    private function isPubliclyVisible(Portofolio $portofolio): bool
    {
        $portofolio->loadMissing('service.freelancer');

        return $portofolio->service?->status === 'Approved'
            && $portofolio->service?->freelancer?->status === 'Approved';
    }
}
