<?php

namespace App\Livewire;

use App\Services\PosOrderHistory;
use App\Support\StorefrontSettings;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PosOrders extends Component
{
    use WithPagination;

    public string $search = '';

    public string $from = '';

    public string $to = '';

    public string $payment = '';

    #[Locked]
    public array $appliedFilters = [];

    #[Locked]
    public ?string $selectedUuid = null;

    public function boot(): void
    {
        Gate::authorize('use-pos');
    }

    public function applyFilters(PosOrderHistory $history): void
    {
        $this->appliedFilters = $history->filters([
            'search' => trim($this->search), 'from' => $this->from, 'to' => $this->to, 'payment' => $this->payment,
        ]);
        $this->resetPage();
        $this->resetErrorBag();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'from', 'to', 'payment', 'appliedFilters');
        $this->resetPage();
        $this->resetErrorBag();
    }

    public function showOrder(string $uuid, PosOrderHistory $history): void
    {
        $history->detail(auth('web')->user(), $uuid);
        $this->selectedUuid = $uuid;
    }

    public function closeOrder(): void
    {
        $this->selectedUuid = null;
    }

    public function render(PosOrderHistory $history)
    {
        $user = auth('web')->user();
        $selectedOrder = $this->selectedUuid ? $history->detail($user, $this->selectedUuid) : null;

        return view('livewire.pos-orders', [
            'orders' => $history->paginate($user, $this->appliedFilters),
            'selectedOrder' => $selectedOrder,
            'summary' => $selectedOrder ? $history->paymentSummary($selectedOrder) : [],
            'paymentLabels' => PosOrderHistory::PAYMENT_LABELS,
            'statusLabels' => PosOrderHistory::STATUS_LABELS,
            'methodLabels' => PosOrderHistory::METHOD_LABELS,
        ])->layout('layouts.pos', ['store' => app(StorefrontSettings::class)->all()]);
    }
}
