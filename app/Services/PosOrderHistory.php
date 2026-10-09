<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Support\ContactFormat;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class PosOrderHistory
{
    public const PAYMENT_LABELS = ['paid' => 'Đã thanh toán', 'unpaid' => 'Chưa thanh toán', 'partially_paid' => 'Thanh toán một phần'];

    public const STATUS_LABELS = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn tất', 'cancelled' => 'Đã hủy'];

    public const METHOD_LABELS = ['cash' => 'Tiền mặt', 'vietqr' => 'VietQR', 'transfer' => 'Chuyển khoản'];

    public function filters(array $input): array
    {
        return Validator::make($input, [
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when(! empty($input['from']), ['after_or_equal:from'])],
            'payment' => ['nullable', Rule::in(array_keys(self::PAYMENT_LABELS))],
            'shift' => ['nullable', Rule::in(['in_shift', 'outside', 'legacy'])],
        ], [
            'search.max' => 'Từ khóa không được quá 100 ký tự.',
            'date_format' => 'Ngày không hợp lệ. Vui lòng chọn lại ngày.',
            'after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
            'payment.in' => 'Trạng thái thanh toán không hợp lệ. Vui lòng chọn lại.',
            'shift.in' => 'Bộ lọc ca không hợp lệ. Vui lòng chọn lại.',
        ])->validate();
    }

    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        Gate::forUser($user)->authorize('use-pos');
        $filters = $this->filters($filters);
        $query = Order::where('channel', 'pos')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('created_by', $user->id))
            ->with('creator:id,name');
        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $phone = ContactFormat::phone($search);
            $query->where(fn ($q) => $q->where('order_code', 'like', '%'.$search.'%')
                ->orWhere('customer_name', 'like', '%'.$search.'%')
                ->orWhere('customer_phone', 'like', '%'.$search.'%')
                ->when(preg_match(ContactFormat::PHONE_PATTERN, $phone), fn ($q) => $q->orWhere('customer_phone', $phone)));
        }
        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from'].' 00:00:00');
        }
        if (! empty($filters['to'])) {
            $query->where('created_at', '<', CarbonImmutable::createFromFormat('!Y-m-d', $filters['to'])->addDay()->format('Y-m-d H:i:s'));
        }

        return $query->when(! empty($filters['payment']), fn ($q) => $q->where('payment_status', $filters['payment']))
            ->when(($filters['shift'] ?? '') === 'outside', fn ($q) => $q->where('pos_outside_shift', true))
            ->when(($filters['shift'] ?? '') === 'in_shift', fn ($q) => $q->whereNotNull('pos_shift_id'))
            ->when(($filters['shift'] ?? '') === 'legacy', fn ($q) => $q->whereNull('pos_shift_id')->where('pos_outside_shift', false))
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(15, [
                'id', 'uuid', 'order_code', 'created_at', 'created_by', 'customer_name', 'customer_phone',
                'payment_status', 'payment_method', 'grand_total', 'receipt_confirmed_at',
                'pos_shift_id', 'pos_outside_shift',
            ]);
    }

    public function detail(User $user, string $uuid): Order
    {
        Validator::make(['order' => $uuid], ['order' => ['required', 'uuid']])->validate();
        $order = Order::where('uuid', $uuid)->firstOrFail();
        Gate::forUser($user)->authorize('view-pos-order', $order);

        return $order->load(['creator:id,name', 'orderItems' => fn ($q) => $q->select([
            'id', 'order_id', 'product_name', 'unit_name', 'quantity', 'unit_price', 'subtotal',
        ])]);
    }

    public function paymentSummary(Order $order): array
    {
        return [
            'change' => $order->payment_method === 'cash' && $order->cash_received !== null
                ? max(0, $order->cash_received - (int) $order->grand_total) : null,
            'remaining' => max(0, (int) $order->grand_total - (int) $order->paid_amount),
        ];
    }
}
