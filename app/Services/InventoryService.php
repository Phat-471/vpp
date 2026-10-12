<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
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
        return DB::transaction(function () use ($order, $newStatus, $extraAttributes): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $this->assertStatusTransition(
                $lockedOrder->status,
                $newStatus,
                ['pending', 'processing', 'shipping', 'delivered', 'completed', 'cancelled'],
                'đơn hàng'
            );

            $updateData = array_merge($extraAttributes, ['status' => $newStatus]);
            $lockedOrder->update($updateData);

            return $lockedOrder->fresh();
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
        return DB::transaction(function () use ($ticket, $newStatus, $extraAttributes): RepairTicket {
            $lockedTicket = RepairTicket::query()->lockForUpdate()->findOrFail($ticket->getKey());
            $this->assertStatusTransition(
                $lockedTicket->status,
                $newStatus,
                ['received', 'diagnosing', 'waiting_approval', 'in_progress', 'completed', 'delivered', 'cancelled'],
                'phiếu sửa chữa'
            );

            $updateData = array_merge($extraAttributes, ['status' => $newStatus]);
            $lockedTicket->update($updateData);

            return $lockedTicket->fresh();
        });
    }

    /** @param list<string> $allowedStatuses */
    private function assertStatusTransition(string $current, string $next, array $allowedStatuses, string $recordLabel): void
    {
        if (! in_array($next, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => "Trạng thái mới của {$recordLabel} không hợp lệ.",
            ]);
        }

        if ($current === 'cancelled' && $next !== 'cancelled') {
            throw ValidationException::withMessages([
                'status' => "{$recordLabel} đã hủy không thể mở lại; hãy tạo hồ sơ mới để giữ đúng lịch sử tồn kho.",
            ]);
        }
    }

    /**
     * Nhận hàng và nhập kho từ phiếu nhập nhà cung cấp (tăng tồn, cập nhật giá vốn, ghi nhận nợ NCC).
     */
    public function receivePurchaseOrder(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order) {
            if ($order->isReceived()) {
                throw new \RuntimeException("Phiếu nhập {$order->code} đã được nhập kho trước đó.");
            }

            $order->load(['items.product', 'supplier']);

            foreach ($order->items as $item) {
                if ($item->product_id && $item->product) {
                    $item->product->addStock((int) $item->quantity);
                    if ($item->cost_price > 0) {
                        $item->product->update(['cost_price' => $item->cost_price]);
                    }
                }
            }

            if ($order->debt_amount > 0 && $order->supplier) {
                $order->supplier->increment('debt_balance', $order->debt_amount);
            }

            $order->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            return $order->fresh();
        });
    }

    /**
     * Hủy phiếu nhập kho (nếu đã nhập thì tự động trừ tồn kho và giảm công nợ NCC đã ghi nhận).
     */
    public function cancelPurchaseOrder(PurchaseOrder $order, ?string $reason = null): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $reason) {
            if ($order->isCancelled()) {
                return $order;
            }

            if ($order->isReceived()) {
                $order->load(['items.product', 'supplier']);

                foreach ($order->items as $item) {
                    if ($item->product_id && $item->product) {
                        $item->product->deductStock((int) $item->quantity);
                    }
                }

                if ($order->debt_amount > 0 && $order->supplier) {
                    $order->supplier->decrement('debt_balance', $order->debt_amount);
                }
            }

            $notes = $order->notes;
            if ($reason) {
                $notes = $notes ? "{$notes}\n[Lý do hủy: {$reason}]" : "[Lý do hủy: {$reason}]";
            }

            $order->update([
                'status' => 'cancelled',
                'notes' => $notes,
            ]);

            return $order->fresh();
        });
    }

    /**
     * Hoàn tất phiếu kiểm kê kho và tự động cân bằng tồn kho theo số lượng thực tế.
     */
    public function completeStockCheck(\App\Models\StockCheck $check): \App\Models\StockCheck
    {
        return DB::transaction(function () use ($check) {
            if ($check->isCompleted()) {
                throw new \RuntimeException("Phiếu kiểm kê {$check->code} đã hoàn tất cân bằng trước đó.");
            }

            $check->load('items.product');

            foreach ($check->items as $item) {
                if ($item->product) {
                    $item->product->update([
                        'stock_quantity' => (int) $item->actual_quantity,
                    ]);
                }
            }

            $check->recalculateTotals();
            $check->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $check->fresh();
        });
    }

    /**
     * Hủy phiếu kiểm kê kho (nếu đã cân bằng thì hoàn tác số lượng tồn kho về trước khi kiểm).
     */
    public function cancelStockCheck(\App\Models\StockCheck $check, ?string $reason = null): \App\Models\StockCheck
    {
        return DB::transaction(function () use ($check, $reason) {
            if ($check->isCancelled()) {
                return $check;
            }

            if ($check->isCompleted()) {
                $check->load('items.product');

                foreach ($check->items as $item) {
                    if ($item->product) {
                        // Hoàn tác tồn kho: trừ đi phần chênh lệch đã điều chỉnh
                        $newStock = (int) $item->product->stock_quantity - (int) $item->difference;
                        $item->product->update([
                            'stock_quantity' => max(0, $newStock),
                        ]);
                    }
                }
            }

            $notes = $check->notes;
            if ($reason) {
                $notes = $notes ? "{$notes}\n[Lý do hủy: {$reason}]" : "[Lý do hủy: {$reason}]";
            }

            $check->update([
                'status' => 'cancelled',
                'notes' => $notes,
            ]);

            return $check->fresh();
        });
    }
}

