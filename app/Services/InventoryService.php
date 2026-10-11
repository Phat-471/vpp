<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\RepairTicket;
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

    /**
     * Hoàn trả tồn kho linh kiện cho phiếu sửa chữa.
     */
    public function refundTicketStock(RepairTicket $ticket): void
    {
        $ticket->load('repairItems.product');

        foreach ($ticket->repairItems as $item) {
            if ($item->product_id && $item->product && $item->quantity > 0) {
                $item->product->addStock((int) $item->quantity);
            }
        }
    }

    /**
     * Trừ tồn kho linh kiện cho phiếu sửa chữa.
     */
    public function deductTicketStock(RepairTicket $ticket, bool $strictCheck = false): void
    {
        $ticket->load('repairItems.product');

        if ($strictCheck) {
            $shortages = [];
            foreach ($ticket->repairItems as $item) {
                if ($item->product_id && $item->product && $item->quantity > 0) {
                    if ($item->product->stock_quantity < $item->quantity) {
                        $shortages[] = "Linh kiện '{$item->item_name}' không đủ tồn kho (Còn {$item->product->stock_quantity}, cần {$item->quantity})";
                    }
                }
            }

            if (!empty($shortages)) {
                throw ValidationException::withMessages([
                    'stock' => implode('; ', $shortages),
                ]);
            }
        }

        foreach ($ticket->repairItems as $item) {
            if ($item->product_id && $item->product && $item->quantity > 0) {
                $item->product->deductStock((int) $item->quantity);
            }
        }
    }

    /**
     * Kiểm tra khả năng đáp ứng tồn kho linh kiện của phiếu sửa chữa.
     */
    public function checkTicketStockAvailability(RepairTicket $ticket): array
    {
        $ticket->load('repairItems.product');
        $shortages = [];
        $isAvailable = true;

        foreach ($ticket->repairItems as $item) {
            if ($item->product_id && $item->product && $item->quantity > 0) {
                if ($item->product->stock_quantity < $item->quantity) {
                    $isAvailable = false;
                    $shortages[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->item_name,
                        'required' => (int) $item->quantity,
                        'available' => (int) $item->product->stock_quantity,
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
     * Cập nhật trạng thái phiếu sửa chữa kèm giao dịch DB toàn vẹn.
     */
    public function updateTicketStatus(RepairTicket $ticket, string $newStatus, array $extraAttributes = []): RepairTicket
    {
        return DB::transaction(function () use ($ticket, $newStatus, $extraAttributes) {
            $updateData = array_merge($extraAttributes, ['status' => $newStatus]);
            $ticket->update($updateData);

            return $ticket->fresh();
        });
    }
}
