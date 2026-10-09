<?php

namespace App\Actions\Pos;

use App\Enums\PosPaymentMethod;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Services\CustomerManagement;
use App\Services\InvoiceDetails;
use App\Services\PosCart;
use App\Services\PosShiftService;
use App\Support\ContactFormat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class CreatePosOrder
{
    public function handle(User $cashier, array $items, array $input, string $saleUuid): Order
    {
        Gate::forUser($cashier)->authorize('use-pos');
        $invoice = app(InvoiceDetails::class)->validate($input);
        $input['customer_phone'] = is_string($input['customer_phone'] ?? null) ? ContactFormat::phone($input['customer_phone']) : ($input['customer_phone'] ?? null);
        $input['sale_uuid'] = $saleUuid;
        $data = Validator::make($input, [
            'sale_uuid' => ['required', 'uuid'],
            'customer_name' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/\p{L}/u'],
            'customer_phone' => ['nullable', 'string', 'regex:'.ContactFormat::PHONE_PATTERN],
            'notes' => ['nullable', 'string', 'max:1000'],
            'discount' => ['required', 'integer', 'min:0'],
            'cash_given' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', Rule::enum(PosPaymentMethod::class)],
        ], [
            'customer_phone.regex' => 'Số điện thoại khách hàng chưa đúng định dạng Việt Nam.',
            'customer_name.min' => 'Tên khách hàng cần ít nhất 2 ký tự.',
            'customer_name.regex' => 'Tên khách hàng cần có chữ cái.',
        ], [
            'customer_name' => 'tên khách hàng', 'customer_phone' => 'số điện thoại',
            'discount' => 'giảm giá', 'cash_given' => 'tiền khách đưa', 'notes' => 'ghi chú',
        ])->validate();

        return DB::transaction(function () use ($cashier, $items, $data, $saleUuid, $invoice) {
            // Serialize this cashier's submissions and make repeated checkout requests idempotent.
            User::whereKey($cashier->id)->lockForUpdate()->firstOrFail();
            $existing = Order::where('uuid', $saleUuid)->first();
            if ($existing) {
                abort_unless($existing->created_by === $cashier->id, 403);

                return $existing;
            }
            $quote = app(PosCart::class)->quote($items, (int) $data['discount'], lock: true);
            $shift = app(PosShiftService::class)->forCheckout($cashier);
            $isCash = $data['payment_method'] === PosPaymentMethod::Cash->value;
            if ($isCash && $data['cash_given'] < $quote['total']) {
                throw ValidationException::withMessages(['cashGiven' => 'Tiền khách đưa chưa đủ thanh toán.']);
            }
            $customer = null;
            if ($data['customer_phone']) {
                $customer = app(CustomerManagement::class)->findByPhone($data['customer_phone']) ?? Customer::firstOrCreate(['phone' => $data['customer_phone']], [
                    'name' => $data['customer_name'] ?: 'Khách mua tại quầy',
                    'phone_last4' => substr($data['customer_phone'], -4),
                ]);
            }
            do {
                $code = 'HD'.random_int(10000000, 99999999);
            } while (Order::where('order_code', $code)->exists());
            $paid = $isCash || $quote['total'] === 0;
            $order = Order::create(array_merge($invoice, [
                'uuid' => $saleUuid, 'order_code' => $code,
                'customer_id' => $customer?->id,
                'customer_name' => $data['customer_name'] ?: ($customer?->name ?? 'Khách lẻ tại quầy'),
                'customer_phone' => $data['customer_phone'] ?: null,
                'channel' => 'pos', 'status' => $paid ? 'completed' : 'pending',
                'pos_shift_id' => $shift?->id, 'pos_outside_shift' => $shift === null,
                'subtotal' => $quote['subtotal'], 'discount_amount' => $quote['discount'],
                'tax_rate' => 0, 'tax_amount' => 0, 'grand_total' => $quote['total'],
                'paid_amount' => $paid ? $quote['total'] : 0,
                'cash_received' => $isCash ? (int) $data['cash_given'] : null,
                'payment_status' => $paid ? 'paid' : 'unpaid',
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?: null, 'created_by' => $cashier->id,
            ]));
            foreach ($quote['lines'] as $line) {
                $order->orderItems()->create([
                    'product_id' => $line['product_id'], 'product_unit_id' => $line['product_unit_id'],
                    'product_name' => $line['name'], 'unit_name' => $line['unit_name'],
                    'quantity' => $line['quantity'], 'conversion_rate' => $line['conversion_rate'],
                    'cost_price' => $line['cost_price'], 'unit_price' => $line['unit_price'],
                    'subtotal' => $line['subtotal'],
                ]);
            }

            return $order->refresh();
        }, 3);
    }
}
