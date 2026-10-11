<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Hoàn trả tồn kho cho tất cả sản phẩm trong đơn hàng.
     */
    public function refundOrderStock(Order $order): void
    {
        $order->load('orderItems.product');

        foreach ($order->orderItems as $item) {
            if ($item->product_id && $item->product) {
                $baseQty = (int) $item->quantity * ($item->conversion_rate ?: 1);
                if ($baseQty > 0) {
                    $item->product->addStock($baseQty);
                }
            }
        }
    }

    /**
     * Trừ tồn kho cho tất cả sản phẩm trong đơn hàng.
     */
    public function deductOrderStock(Order $order, bool $strictCheck = false): void
    {
        $order->load('orderItems.product');

        if ($strictCheck) {
            $shortages = [];
            foreach ($order->orderItems as $item) {
                if ($item->product_id && $item->product) {
                    $baseQty = (int) $item->quantity * ($item->conversion_rate ?: 1);
                    if ($item->product->stock_quantity < $baseQty) {
                        $shortages[] = "Sản phẩm '{$item->product->name}' không đủ tồn kho (Còn {$item->product->stock_quantity}, cần {$baseQty})";
                    }
                }
            }

            if (!empty($shortages)) {
                throw ValidationException::withMessages([
                    'stock' => implode('; ', $shortages),
                ]);
            }
        }

        foreach ($order->orderItems as $item) {
            if ($item->product_id && $item->product) {
                $baseQty = (int) $item->quantity * ($item->conversion_rate ?: 1);
                if ($baseQty > 0) {
                    $item->product->deductStock($baseQty);
                }
            }
        }
    }

    /**
     * Kiểm tra khả năng đáp ứng tồn kho của đơn hàng.
     */
    public function checkOrderStockAvailability(Order $order): array
    {
        $order->load('orderItems.product');
        $shortages = [];
        $isAvailable = true;

        foreach ($order->orderItems as $item) {
            if ($item->product_id && $item->product) {
                $baseQty = (int) $item->quantity * ($item->conversion_rate ?: 1);
                if ($item->product->stock_quantity < $baseQty) {
                    $isAvailable = false;
                    $shortages[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'required' => $baseQty,
                        'available' => $item->product->stock_quantity,
                    ];
                }
            }
        }

        return [
            'is_available' => $isAvailable,
            'shortages' => $shortages,
        ];
    }

    /**
     * Cập nhật trạng thái đơn hàng kèm giao dịch DB toàn vẹn.
     */
    public function updateOrderStatus(Order $order, string $newStatus, array $extraAttributes = []): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $extraAttributes) {
            $updateData = array_merge($extraAttributes, ['status' => $newStatus]);
            $order->update($updateData);

            return $order->fresh();
        });
    }
}
