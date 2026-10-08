<?php

namespace App\Services;

use App\Enums\PosShiftStatus;
use App\Models\PosShift;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PosShiftService
{
    public function required(): bool
    {
        return (string) Setting::get('pos_shift_required', '1') !== '0';
    }

    public function configure(User $actor, bool $required): void
    {
        abort_unless($actor->isAdmin(), 403);
        Setting::set('pos_shift_required', $required ? '1' : '0', 'pos', 'boolean');
    }

    public function current(User $user, bool $lock = false): ?PosShift
    {
        Gate::forUser($user)->authorize('use-pos');

        return PosShift::where('active_cashier_id', $user->id)->where('status', PosShiftStatus::Open)
            ->when($lock, fn ($q) => $q->lockForUpdate())->first();
    }

    // Called inside checkout's transaction, after locking the cashier row.
    public function forCheckout(User $user): ?PosShift
    {
        $shift = $this->current($user, true);
        if (! $shift && $this->required()) {
            throw ValidationException::withMessages(['shift' => 'Chưa mở ca hoặc ca đã đóng. Vui lòng mở ca trước khi thanh toán.']);
        }

        return $shift;
    }

    public function open(User $user, mixed $openingCash): PosShift
    {
        Gate::forUser($user)->authorize('use-pos');
        $cash = $this->cash($openingCash, 'openingCash');

        return DB::transaction(function () use ($user, $cash) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($this->current($user, true)) {
                throw ValidationException::withMessages(['shift' => 'Đang có một ca mở. Vui lòng đóng ca hiện tại trước.']);
            }

            return PosShift::create([
                'uuid' => (string) Str::uuid(), 'cashier_id' => $user->id, 'active_cashier_id' => $user->id,
                'status' => PosShiftStatus::Open, 'opened_at' => now(), 'opening_cash' => $cash,
            ]);
        }, 3);
    }

    public function close(User $user, string $uuid, mixed $countedCash, string $notes): PosShift
    {
        Gate::forUser($user)->authorize('use-pos');
        Validator::make(['uuid' => $uuid, 'notes' => $notes], ['uuid' => 'required|uuid', 'notes' => 'nullable|string|max:1000'])->validate();
        $cash = $this->cash($countedCash, 'countedCash');

        return DB::transaction(function () use ($user, $uuid, $cash, $notes) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $shift = PosShift::where('uuid', $uuid)->lockForUpdate()->firstOrFail();
            Gate::forUser($user)->authorize('update', $shift);
            if ($shift->status === PosShiftStatus::Closed) {
                return $shift; // Repeated request cannot alter a closed reconciliation.
            }
            $orders = $shift->orders()->orderBy('id')->lockForUpdate()->get();
            $summary = $this->totals($shift, $orders);
            $difference = $cash * 100 - $this->minor($summary['expected_cash']);
            if ($difference !== 0 && mb_strlen(trim($notes)) < 5) {
                throw ValidationException::withMessages(['notes' => 'Có chênh lệch tiền mặt. Vui lòng nhập lý do ít nhất 5 ký tự.']);
            }
            foreach ($orders as $order) {
                $order->update(['pos_shift_paid_at_close' => $order->paid_amount]);
            }
            $shift->update($summary + [
                'status' => PosShiftStatus::Closed, 'active_cashier_id' => null, 'closed_at' => now(),
                'counted_cash' => $cash, 'difference' => $this->decimal($difference), 'notes' => trim($notes) ?: null,
            ]);

            return $shift->refresh();
        }, 3);
    }

    public function summary(PosShift $shift): array
    {
        $orders = $shift->orders()->get(['id', 'pos_shift_id', 'status', 'payment_status', 'payment_method', 'grand_total', 'paid_amount', 'pos_shift_paid_at_close']);
        $summary = $shift->status === PosShiftStatus::Open ? $this->totals($shift, $orders)
            : $shift->only(['cash_sales', 'transfer_sales', 'total_sales', 'expected_cash', 'order_count', 'pending_transfer_count']);
        $late = 0;
        if ($shift->status === PosShiftStatus::Closed) {
            foreach ($orders as $order) {
                if ($order->payment_method !== 'cash' && $order->status !== 'cancelled') {
                    $late += max(0, $this->minor($order->paid_amount) - $this->minor($order->pos_shift_paid_at_close ?? '0'));
                }
            }
        }

        return $summary + ['late_transfer' => $this->decimal($late)];
    }

    private function totals(PosShift $shift, Collection $orders): array
    {
        $cash = $transfer = $total = $pending = $count = 0;
        foreach ($orders as $order) {
            if ($order->status === 'cancelled') {
                continue;
            }
            $count++;
            $total += $this->minor($order->grand_total);
            if ($order->payment_method === 'cash') {
                $cash += $this->minor($order->paid_amount);
            } else {
                $transfer += $this->minor($order->paid_amount);
                $pending += (int) ($order->payment_status !== 'paid');
            }
        }

        return [
            'cash_sales' => $this->decimal($cash), 'transfer_sales' => $this->decimal($transfer),
            'total_sales' => $this->decimal($total), 'expected_cash' => $this->decimal($this->minor($shift->opening_cash) + $cash),
            'order_count' => $count, 'pending_transfer_count' => $pending,
        ];
    }

    private function cash(mixed $value, string $field): int
    {
        $data = Validator::make([$field => $value], [$field => ['required', 'integer', 'min:0', 'max:1000000000000']], [
            'required' => 'Vui lòng nhập số tiền.', 'integer' => 'Số tiền phải là số nguyên theo đồng Việt Nam.',
            'min' => 'Số tiền không được âm.', 'max' => 'Số tiền vượt giới hạn cho phép.',
        ])->validate();

        return (int) $data[$field];
    }

    private function minor(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return (int) $whole * 100 + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function decimal(int $amount): string
    {
        return ($amount < 0 ? '-' : '').intdiv(abs($amount), 100).'.'.str_pad((string) (abs($amount) % 100), 2, '0', STR_PAD_LEFT);
    }
}
