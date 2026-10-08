<?php

namespace App\Policies;

use App\Models\PosShift;
use App\Models\User;

class PosShiftPolicy
{
    public function view(User $user, PosShift $shift): bool
    {
        return $user->isCashier() && ($user->isAdmin() || $shift->cashier_id === $user->id);
    }

    public function update(User $user, PosShift $shift): bool
    {
        return $user->isCashier() && $shift->cashier_id === $user->id;
    }
}
