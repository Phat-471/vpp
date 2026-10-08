<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Services\CustomerManagement;
use App\Support\StorefrontSettings;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PosCustomers extends Component
{
    use WithPagination;

    public string $search = '';

    public array $form = ['name' => '', 'phone' => '', 'email' => '', 'address' => '', 'notes' => ''];

    public bool $formOpen = false;

    #[Locked]
    public ?int $editingId = null;

    #[Locked]
    public bool $selectForSale = false;

    #[Locked]
    public string $message = '';

    public function boot(): void
    {
        Gate::authorize('viewAny', Customer::class);
    }

    public function mount(bool $selectForSale = false): void
    {
        $this->selectForSale = $selectForSale;
    }

    public function updatedSearch(): void
    {
        $this->resetPage('customersPage');
    }

    public function newCustomer(): void
    {
        Gate::authorize('create', Customer::class);
        $this->reset('form', 'editingId', 'message');
        $this->resetErrorBag();
        $this->formOpen = true;
    }

    public function editCustomer(int $id): void
    {
        $customer = Customer::findOrFail($id);
        Gate::authorize('update', $customer);
        $this->reset('form');
        foreach (['name', 'phone', 'email', 'address', 'notes'] as $field) {
            $this->form[$field] = (string) $customer->getAttribute($field);
        }
        $this->editingId = $id;
        $this->resetErrorBag();
        $this->formOpen = true;
    }

    public function saveCustomer(CustomerManagement $service): void
    {
        try {
            $customer = $service->save($this->form, $this->editingId);
        } catch (LockTimeoutException) {
            $this->addError('customer', 'Hệ thống đang bận. Vui lòng lưu lại sau ít giây.');

            return;
        }
        $this->formOpen = false;
        $this->message = 'Đã lưu thông tin khách hàng.';
        if ($this->selectForSale) {
            $this->selectCustomer($customer->id);
        }
    }

    public function deleteCustomer(int $id, CustomerManagement $service): void
    {
        $service->delete($id);
        $this->resetErrorBag();
        $this->formOpen = false;
        $this->message = 'Đã xóa khách hàng chưa có giao dịch.';
    }

    public function selectCustomer(int $id): void
    {
        abort_unless($this->selectForSale, 403);
        Gate::authorize('view', Customer::findOrFail($id));
        $this->dispatch('pos-customer-selected', id: $id)->to(PosTerminal::class);
    }

    public function render()
    {
        $keyword = '%'.mb_substr(trim($this->search), 0, 100).'%';
        $customers = Customer::select(['id', 'name', 'phone', 'email', 'address'])
            ->withCount(['orders', 'repairTickets'])
            ->when(trim($this->search) !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', $keyword)->orWhere('phone', 'like', $keyword)->orWhere('email', 'like', $keyword)))
            ->orderByDesc('id')->paginate(10, ['*'], 'customersPage');

        return view('livewire.pos-customers', compact('customers'))
            ->layout('layouts.pos', ['store' => app(StorefrontSettings::class)->all()]);
    }
}
