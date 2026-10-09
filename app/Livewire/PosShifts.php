<?php

namespace App\Livewire;

use App\Models\PosShift;
use App\Services\PosShiftService;
use App\Support\StorefrontSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PosShifts extends Component
{
    use WithPagination;

    public string $openingCash = '';

    public string $countedCash = '';

    public string $notes = '';

    public bool $requiredEnabled = true;

    #[Locked]
    public bool $compact = false;

    #[Locked]
    public ?string $selectedUuid = null;

    #[Locked]
    public string $message = '';

    public function boot(): void
    {
        Gate::authorize('use-pos');
    }

    public function mount(bool $compact = false): void
    {
        $this->compact = $compact;
        $this->requiredEnabled = app(PosShiftService::class)->required();
        $this->selectedUuid = app(PosShiftService::class)->current(auth('web')->user())?->uuid;
    }

    public function openShift(PosShiftService $service): void
    {
        $shift = $service->open(auth('web')->user(), $this->openingCash);
        $this->selectedUuid = $shift->uuid;
        $this->reset('openingCash', 'countedCash', 'notes');
        $this->resetErrorBag();
        $this->message = 'Đã mở ca. Có thể bắt đầu bán hàng.';
        $this->dispatch('pos-shift-changed')->to(PosTerminal::class);
    }

    public function closeShift(PosShiftService $service): void
    {
        abort_unless($this->selectedUuid && ! $this->compact, 403);
        $service->close(auth('web')->user(), $this->selectedUuid, $this->countedCash, $this->notes);
        $this->resetErrorBag();
        $this->message = 'Đã đóng ca và chốt đối soát tiền mặt.';
        $this->dispatch('pos-shift-changed')->to(PosTerminal::class);
    }

    public function showShift(string $uuid): void
    {
        Validator::make(['uuid' => $uuid], ['uuid' => 'required|uuid'])->validate();
        $shift = PosShift::where('uuid', $uuid)->firstOrFail();
        Gate::authorize('view', $shift);
        $this->selectedUuid = $uuid;
        $this->reset('countedCash', 'notes');
        $this->resetErrorBag();
    }

    public function saveMode(PosShiftService $service): void
    {
        $service->configure(auth('web')->user(), $this->requiredEnabled);
        $this->message = 'Đã cập nhật yêu cầu mở ca. Các đơn cũ giữ nguyên ca đã ghi nhận.';
        $this->dispatch('pos-shift-changed')->to(PosTerminal::class);
    }

    public function render(PosShiftService $service)
    {
        $user = auth('web')->user();
        $current = $service->current($user);
        $selected = $this->selectedUuid ? PosShift::with('cashier:id,name')->where('uuid', $this->selectedUuid)->firstOrFail() : $current;
        if ($selected) {
            Gate::authorize('view', $selected);
        }

        return view('livewire.pos-shifts', [
            'current' => $current, 'selected' => $selected,
            'summary' => $selected && ! $this->compact ? $service->summary($selected) : [],
            'required' => $service->required(),
            'shifts' => $this->compact ? null : PosShift::with('cashier:id,name')->when(! $user->isAdmin(), fn ($q) => $q->where('cashier_id', $user->id))
                ->orderByDesc('id')->paginate(15, ['*'], 'shiftsPage'),
        ])->layout('layouts.pos', ['store' => app(StorefrontSettings::class)->all()]);
    }
}
