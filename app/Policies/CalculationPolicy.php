<?php

namespace App\Policies;

use App\Models\Calculation;
use App\Models\User;

class CalculationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Calculation $calculation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Calculation $calculation): bool
    {
        return $user->isMaster() || $user->id === $calculation->owner_id;
    }

    public function delete(User $user, Calculation $calculation): bool
    {
        return $user->isMaster() || $user->id === $calculation->owner_id;
    }
}
