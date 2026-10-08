<?php

namespace App\Livewire;

use App\Actions\Pos\CreatePosOrder;
use App\Exceptions\TaxLookupUnavailable;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\BusinessTaxLookup;
use App\Services\PosCart;
use App\Services\PosPayment;
use App\Support\ContactFormat;
use App\Support\StorefrontSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PosTerminal extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $selectedCategory = null;

    public string $discount = '0';

    public string $cashGiven = '0';

    public string $customerName = '';

    public string $customerPhone = '';

    public bool $customerManagerOpen = false;

    #[On('pos-customer-selected')]
    public function selectCustomer(int $id): void
    {
        if ($this->completedOrderUuid) {
            return;
        }
        $customer = Customer::findOrFail($id);
        Gate::authorize('view', $customer);
        $this->customerName = $customer->name;
        $this->customerPhone = ContactFormat::phone($customer->phone);
        $this->customerManagerOpen = false;
    }

    public string $notes = '';

    public string $paymentMethod = 'cash';

    public string $message = '';

    public bool $isVatInvoice = false;

    public string $companyTaxId = '';

    public string $companyName = '';

    public string $companyAddress = '';

    public string $invoiceEmail = '';

    #[Locked]
    public string $taxLookupMessage = '';

    public function updatedCompanyTaxId(): void
    {
        $this->companyName = '';
        $this->companyAddress = '';
        $this->taxLookupMessage = '';
        if (preg_match('/^(?:[0-9]{10}(?:-?[0-9]{3})?|[0-9]{12})$/D', trim($this->companyTaxId))) {
            $this->lookupCompany();
        }
    }

    public function lookupCompany(): void
    {
        if (! $this->isVatInvoice || $this->completedOrderUuid) {
            return;
        }
        $this->companyName = '';
        $this->companyAddress = '';
        try {
            $data = app(BusinessTaxLookup::class)->find($this->companyTaxId);
            if ($data) {
                $this->companyTaxId = $data['tax_code'];
                $this->companyName = $data['name'];
                $this->companyAddress = $data['address'];
            }
            $this->taxLookupMessage = $data ? 'Đã điền tên và địa chỉ. Vui lòng kiểm tra lại.' : 'Không tìm thấy doanh nghiệp. Có thể nhập thủ công.';
        } catch (TaxLookupUnavailable $e) {
            $this->taxLookupMessage = $e->getMessage();
        }
    }

    #[Locked]
    public array $cart = [];

    #[Locked]
    public string $saleUuid = '';

    #[Locked]
    public ?string $completedOrderUuid = null;

    #[Locked]
    public bool $receiptPrintRequested = false;

    public function boot(): void
    {
        Gate::authorize('use-pos');
    }

    public function mount(): void
    {
        $this->saleUuid = (string) Str::uuid();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId, ?int $unitId = null): void
    {
        if ($this->completedOrderUuid) {
            return;
        }
        $items = $this->cart;
        $items[] = ['product_id' => $productId, 'product_unit_id' => $unitId ?: null, 'quantity' => 1];
        $this->cart = app(PosCart::class)->quote($items)['lines'];
        $this->resetErrorBag();
    }

    public function updateQuantity(string $key, int $delta): void
    {
        if ($this->completedOrderUuid || ! isset($this->cart[$key]) || ! in_array($delta, [-1, 1], true)) {
            return;
        }
        $items = $this->cart;
        $items[$key]['quantity'] += $delta;
        if ($items[$key]['quantity'] < 1) {
            unset($items[$key]);
        }
        $this->cart = $items ? app(PosCart::class)->quote($items)['lines'] : [];
        $this->resetErrorBag();
    }

    public function changeUnit(string $key, int $unitId): void
    {
        if ($this->completedOrderUuid || ! isset($this->cart[$key])) {
            return;
        }
        $items = $this->cart;
        $items[$key]['product_unit_id'] = $unitId ?: null;
        $this->cart = app(PosCart::class)->quote($items)['lines'];
        $this->resetErrorBag();
    }

    public function removeFromCart(string $key): void
    {
        if (! $this->completedOrderUuid) {
            unset($this->cart[$key]);
            $this->resetErrorBag();
        }
    }

    public function scan(): void
    {
        $product = Product::where('is_active', true)->where(function ($query) {
            $query->where('barcode', trim($this->search))->orWhere('sku', trim($this->search));
        })->first();
        if ($product && trim($this->search) !== '') {
            $this->addToCart($product->id);
            $this->search = '';
            $this->resetPage();
        }
    }

    public function saveDraft(): void
    {
        if (! $this->cart || $this->completedOrderUuid) {
            return;
        }
        $this->validate(['customerName' => 'string|max:100', 'customerPhone' => 'string|max:20', 'notes' => 'string|max:1000', 'discount' => 'required|integer|min:0']);
        session()->put($this->draftKey(), [
            'cart' => $this->cart, 'customerName' => $this->customerName, 'customerPhone' => $this->customerPhone,
            'notes' => $this->notes, 'discount' => $this->discount,
            'invoice' => ['isVatInvoice' => $this->isVatInvoice, 'companyTaxId' => $this->companyTaxId, 'companyName' => $this->companyName, 'companyAddress' => $this->companyAddress, 'invoiceEmail' => $this->invoiceEmail],
        ]);
        $this->newSale();
        $this->message = 'Đã lưu đơn tạm trong phiên làm việc. Bấm “Mở đơn tạm” để tiếp tục.';
    }

    public function restoreDraft(): void
    {
        if ($this->cart || $this->completedOrderUuid || ! ($draft = session($this->draftKey()))) {
            return;
        }
        $this->cart = app(PosCart::class)->quote($draft['cart'])['lines'];
        foreach (['customerName', 'customerPhone', 'notes', 'discount'] as $field) {
            $this->{$field} = $draft[$field];
        }
        session()->forget($this->draftKey());
        foreach ($draft['invoice'] ?? [] as $field => $value) {
            if (in_array($field, ['isVatInvoice', 'companyTaxId', 'companyName', 'companyAddress', 'invoiceEmail'], true)) {
                $this->{$field} = $value;
            }
        }
        $this->message = 'Đã mở lại đơn tạm.';
    }

    public function checkout(): void
    {
        if ($this->completedOrderUuid) {
            return;
        }
        $this->validate([
            'discount' => 'required|integer|min:0', 'cashGiven' => 'required|integer|min:0',
            'customerName' => 'string|max:100', 'customerPhone' => 'string|max:20', 'notes' => 'string|max:1000',
            'paymentMethod' => 'required|in:cash,vietqr',
        ]);
        $order = app(CreatePosOrder::class)->handle(auth('web')->user(), $this->cart, [
            'customer_name' => $this->customerName, 'customer_phone' => $this->customerPhone,
            'notes' => $this->notes, 'discount' => (int) $this->discount, 'cash_given' => (int) $this->cashGiven,
            'payment_method' => $this->paymentMethod,
            'is_vat_invoice' => $this->isVatInvoice, 'company_tax_id' => $this->companyTaxId,
            'company_name' => $this->companyName, 'company_address' => $this->companyAddress, 'invoice_email' => $this->invoiceEmail,
        ], $this->saleUuid);
        $this->completedOrderUuid = $order->uuid;
        $this->resetErrorBag();
        $this->requestReceiptPrint();
    }

    public function newSale(): void
    {
        if ($this->completedOrderUuid) {
            $order = $this->completedOrder();
            if ($order->payment_status !== 'paid' || ! $order->receipt_confirmed_at) {
                $this->addError('receipt', 'Cần thanh toán và xác nhận đã in hóa đơn trước khi tạo đơn tiếp theo.');

                return;
            }
        }
        $this->reset(['cart', 'discount', 'cashGiven', 'customerName', 'customerPhone', 'notes', 'paymentMethod', 'completedOrderUuid', 'message']);
        $this->receiptPrintRequested = false;
        $this->reset(['isVatInvoice', 'companyTaxId', 'companyName', 'companyAddress', 'invoiceEmail', 'taxLookupMessage']);
        $this->saleUuid = (string) Str::uuid();
        $this->resetErrorBag();
    }

    public function refreshPayment(): void
    {
        // Rendering reads the latest payment state; the browser cannot mark an order paid.
        $this->requestReceiptPrint();
    }

    public function requestReceiptPrint(): void
    {
        if (! $this->completedOrderUuid || $this->receiptPrintRequested) {
            return;
        }
        $order = $this->completedOrder();
        if ($order->payment_status !== 'paid') {
            return;
        }
        $this->receiptPrintRequested = true;
        $this->dispatch('pos-print-receipt', url: route('pos.receipt', $order->uuid, absolute: false).'?autoprint=1');
    }

    public function reprintReceipt(): void
    {
        $this->receiptPrintRequested = false;
        $this->requestReceiptPrint();
    }

    public function confirmReceiptPrinted(): void
    {
        if (! $this->completedOrderUuid) {
            return;
        }
        $order = $this->completedOrder();
        if ($order->payment_status !== 'paid' || ! $this->receiptPrintRequested) {
            $this->addError('receipt', 'Cần thanh toán và mở hộp thoại in trước khi xác nhận.');

            return;
        }
        if (! $order->receipt_confirmed_at) {
            $order->update(['receipt_confirmed_at' => now()]);
        }
        $this->resetErrorBag('receipt');
    }

    private function completedOrder(): Order
    {
        $order = Order::where('uuid', $this->completedOrderUuid)->firstOrFail();
        Gate::authorize('view-pos-order', $order);

        return $order;
    }

    private function draftKey(): string
    {
        return 'pos.draft.'.auth('web')->id();
    }

    public function render()
    {
        $products = Product::with(['category', 'units'])->where('is_active', true)
            ->when(trim($this->search) !== '', function ($query) {
                $keyword = '%'.mb_substr(trim($this->search), 0, 100).'%';
                $query->where(fn ($q) => $q->where('name', 'like', $keyword)->orWhere('sku', 'like', $keyword)->orWhere('barcode', 'like', $keyword));
            })
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->orderBy('name')->paginate(18);
        $cartProducts = Product::with('units')->whereIn('id', array_column($this->cart, 'product_id'))->get()->keyBy('id');
        $subtotal = array_sum(array_column($this->cart, 'subtotal'));
        $total = max(0, $subtotal - max(0, (int) $this->discount));
        $completedOrder = $this->completedOrderUuid ? Order::where('uuid', $this->completedOrderUuid)->firstOrFail() : null;
        if ($completedOrder) {
            Gate::authorize('view-pos-order', $completedOrder);
        }

        return view('livewire.pos-terminal', [
            'products' => $products, 'cartProducts' => $cartProducts,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'subtotal' => $subtotal, 'total' => $total, 'change' => max(0, (int) $this->cashGiven - $total),
            'completedOrder' => $completedOrder,
            'qrUrl' => $completedOrder ? app(PosPayment::class)->qrUrl($completedOrder) : null,
            'hasDraft' => session()->has($this->draftKey()),
        ])->layout('layouts.pos', ['store' => app(StorefrontSettings::class)->all()]);
    }
}
