<?php

namespace App\Policies;

use App\Models\SkomdaStudent;

class SkomdaStudentPolicy
{
    public function viewAny($user): bool
    {
        return in_array($this->role($user), ['administrator', 'freelancer'], true);
    }

    public function view($user, SkomdaStudent $skomdaStudent): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function create($user): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function update($user, SkomdaStudent $skomdaStudent): bool
    {
        return $this->role($user) === 'administrator';
    }

    public function delete($user, SkomdaStudent $skomdaStudent): bool
    {
        return $this->role($user) === 'administrator';
    }

    private function role($user): ?string
    {
        return is_object($user) && method_exists($user, 'getRole') ? $user->getRole() : null;
    }
}
