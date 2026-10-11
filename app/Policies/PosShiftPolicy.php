<?php

namespace App\Policies;

use App\Models\PosShift;
use App\Models\User;

class PosShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCashier();
    }

    public function view(User $user, PosShift $shift): bool
    {
        return $user->isCashier() && ($user->isAdmin() || $shift->cashier_id === $user->id);
    }

    public function create(User $user): bool
    {
        return false; // Ca được mở thông qua giao diện quầy bán hàng POS
    }

    public function update(User $user, PosShift $shift): bool
    {
        return $user->isCashier() && $shift->cashier_id === $user->id;
    }

    public function delete(User $user, PosShift $shift): bool
    {
        return false; // Cấm xóa ca để bảo toàn lịch sử đối soát tài chính
    }
}
