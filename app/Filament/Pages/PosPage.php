<?php

namespace App\Filament\Pages;

use App\Helpers\AppHelper;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductUnit;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class PosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Bán Hàng & Thu Ngân';

    protected static ?string $navigationLabel = 'Màn hình Bán Hàng (POS)';

    protected static ?string $title = 'Quầy Thu Ngân Bán Lẻ (POS)';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.pos-page';

    // Livewire reactive properties
    public string $search = '';
    public ?int $selectedCategory = null;
    public array $cart = [];
    public float $discount = 0;
    public ?int $customerId = null;
    public string $customerName = '';
    public string $customerPhone = '';
    public string $paymentMethod = 'cash';
    public float $cashGiven = 0;
    public ?int $completedOrderId = null;
    public ?string $completedOrderCode = null;
    public ?string $vietQrUrl = null;
    public bool $showSuccessModal = false;

    public function mount(): void
    {
        $this->cart = [];
    }

    public function addToCart(int $productId, ?int $unitId = null): void
    {
        $product = Product::with('units')->find($productId);
        if (!$product) {
            return;
        }

        $unitName = $product->base_unit;
        $unitPrice = (float) $product->retail_price;
        $conversionRate = 1;
        $cartKey = "p_{$productId}_base";

        if ($unitId) {
            $unit = ProductUnit::where('product_id', $productId)->find($unitId);
            if ($unit) {
                $unitName = $unit->unit_name;
                $unitPrice = (float) $unit->price;
                $conversionRate = (int) $unit->conversion_rate;
                $cartKey = "p_{$productId}_u_{$unitId}";
            }
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
            $this->cart[$cartKey]['subtotal'] = $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'product_unit_id' => $unitId,
                'name' => $product->name,
                'unit_name' => $unitName,
                'quantity' => 1,
                'conversion_rate' => $conversionRate,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice,
                'stock' => $product->stock_quantity,
            ];
        }

        Notification::make()
            ->title("Đã thêm: {$product->name}")
            ->success()
            ->duration(1200)
            ->send();
    }

    public function updateQuantity(string $cartKey, int $delta): void
    {
        if (!isset($this->cart[$cartKey])) {
            return;
        }

        $this->cart[$cartKey]['quantity'] += $delta;

        if ($this->cart[$cartKey]['quantity'] <= 0) {
            unset($this->cart[$cartKey]);
        } else {
            $this->cart[$cartKey]['subtotal'] = $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];
        }
    }

    public function removeFromCart(string $cartKey): void
    {
        unset($this->cart[$cartKey]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->cashGiven = 0;
        $this->showSuccessModal = false;
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, $this->subtotal - $this->discount);
    }

    public function getChangeAmountProperty(): float
    {
        return max(0, $this->cashGiven - $this->grandTotal);
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Giỏ hàng đang trống!')->danger()->send();
            return;
        }

        // Database Transaction - Standard Rule #6 in AGENTS.md
        DB::beginTransaction();
        try {
            // Find or create customer if phone provided
            $customerId = null;
            if (!empty($this->customerPhone)) {
                $cleaned = AppHelper::cleanPhone($this->customerPhone);
                $customer = Customer::firstOrCreate(
                    ['phone' => $cleaned],
                    [
                        'name' => $this->customerName ?: 'Khách mua tại quầy',
                        'phone_last4' => substr($cleaned, -4),
                    ]
                );
                $customerId = $customer->id;
            }

            $order = Order::create([
                'customer_id' => $customerId,
                'customer_name' => $this->customerName ?: 'Khách lẻ tại quầy',
                'customer_phone' => $this->customerPhone ?: null,
                'channel' => 'pos',
                'status' => 'completed',
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discount,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'grand_total' => $this->grandTotal,
                'paid_amount' => $this->grandTotal,
                'payment_status' => 'paid',
                'payment_method' => $this->paymentMethod,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['product_unit_id'],
                    'product_name' => $item['name'],
                    'unit_name' => $item['unit_name'],
                    'quantity' => $item['quantity'],
                    'conversion_rate' => $item['conversion_rate'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            $this->completedOrderId = $order->id;
            $this->completedOrderCode = $order->order_code;
            $this->vietQrUrl = AppHelper::generateVietQrUrl($order->grand_total, $order->order_code);
            $this->showSuccessModal = true;

            Notification::make()
                ->title('Thanh toán đơn hàng thành công!')
                ->body("Mã hóa đơn: {$order->order_code} - Tổng tiền: " . AppHelper::formatMoney($order->grand_total))
                ->success()
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Lỗi xử lý đơn hàng')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetAndNewSale(): void
    {
        $this->clearCart();
        $this->customerName = '';
        $this->customerPhone = '';
        $this->completedOrderId = null;
        $this->completedOrderCode = null;
        $this->vietQrUrl = null;
        $this->showSuccessModal = false;
    }

    public function getViewData(): array
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $query = Product::with(['category', 'units'])->where('is_active', true);

        if (!empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                  ->orWhere('sku', 'LIKE', "%{$s}%")
                  ->orWhere('barcode', 'LIKE', "%{$s}%");
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $products = $query->orderBy('name')->take(30)->get();

        return [
            'categories' => $categories,
            'products' => $products,
            'subtotal' => $this->subtotal,
            'grandTotal' => $this->grandTotal,
            'changeAmount' => $this->changeAmount,
        ];
    }
}
