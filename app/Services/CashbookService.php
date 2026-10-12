<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashbookService
{
    /**
     * Tạo phiếu thu tiền (Receipt) và tự động giảm trừ công nợ khách hàng nếu có.
     */
    public function createReceipt(array $data): CashTransaction
    {
        return DB::transaction(function () use ($data) {
            $amount = (float) ($data['amount'] ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['amount' => 'Số tiền thu phải lớn hơn 0đ.']);
            }

            $customerId = $data['customer_id'] ?? null;
            $category = $data['category'] ?? 'other_receipt';

            if ($customerId && $category === 'customer_debt') {
                $customer = Customer::lockForUpdate()->find($customerId);
                if ($customer) {
                    $customer->decrement('debt_balance', min($customer->debt_balance, $amount));
                }
            }

            return CashTransaction::create([
                'code' => $data['code'] ?? CashTransaction::generateNextCode('receipt'),
                'type' => 'receipt',
                'category' => $category,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'customer_id' => $customerId,
                'supplier_id' => null,
                'order_id' => $data['order_id'] ?? null,
                'repair_ticket_id' => $data['repair_ticket_id'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
                'status' => 'completed',
                'performed_at' => $data['performed_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Tạo phiếu chi tiền (Payment) và tự động giảm trừ công nợ nhà cung cấp nếu có.
     */
    public function createPayment(array $data): CashTransaction
    {
        return DB::transaction(function () use ($data) {
            $amount = (float) ($data['amount'] ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['amount' => 'Số tiền chi phải lớn hơn 0đ.']);
            }

            $supplierId = $data['supplier_id'] ?? null;
            $category = $data['category'] ?? 'other_payment';

            if ($supplierId && $category === 'supplier_debt') {
                $supplier = Supplier::lockForUpdate()->find($supplierId);
                if ($supplier) {
                    $supplier->decrement('debt_balance', min($supplier->debt_balance, $amount));
                }
            }

            return CashTransaction::create([
                'code' => $data['code'] ?? CashTransaction::generateNextCode('payment'),
                'type' => 'payment',
                'category' => $category,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'customer_id' => null,
                'supplier_id' => $supplierId,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
                'status' => 'completed',
                'performed_at' => $data['performed_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Hủy phiếu thu/chi và hoàn tác công nợ khách hàng hoặc NCC.
     */
    public function cancelTransaction(CashTransaction $transaction, ?string $reason = null): CashTransaction
    {
        return DB::transaction(function () use ($transaction, $reason) {
            if ($transaction->isCancelled()) {
                return $transaction;
            }

            if ($transaction->isReceipt() && $transaction->customer_id && $transaction->category === 'customer_debt') {
                $customer = Customer::lockForUpdate()->find($transaction->customer_id);
                if ($customer) {
                    $customer->increment('debt_balance', $transaction->amount);
                }
            }

            if ($transaction->isPayment() && $transaction->supplier_id && $transaction->category === 'supplier_debt') {
                $supplier = Supplier::lockForUpdate()->find($transaction->supplier_id);
                if ($supplier) {
                    $supplier->increment('debt_balance', $transaction->amount);
                }
            }

            $notes = $transaction->notes;
            if ($reason) {
                $notes = $notes ? "{$notes}\n[Lý do hủy: {$reason}]" : "[Lý do hủy: {$reason}]";
            }

            $transaction->update([
                'status' => 'cancelled',
                'notes' => $notes,
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Tính toán tổng quan quỹ tiền mặt và dòng tiền.
     */
    public function getCashSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = CashTransaction::where('status', 'completed');
        if ($startDate) {
            $query->where('performed_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('performed_at', '<=', $endDate);
        }

        $totalReceipt = (float) (clone $query)->where('type', 'receipt')->sum('amount');
        $totalPayment = (float) (clone $query)->where('type', 'payment')->sum('amount');
        $cashBalance = $totalReceipt - $totalPayment;

        $cashMethodBalance = (float) (clone $query)->where('type', 'receipt')->where('payment_method', 'cash')->sum('amount')
            - (float) (clone $query)->where('type', 'payment')->where('payment_method', 'cash')->sum('amount');

        $bankMethodBalance = (float) (clone $query)->where('type', 'receipt')->where('payment_method', 'bank_transfer')->sum('amount')
            - (float) (clone $query)->where('type', 'payment')->where('payment_method', 'bank_transfer')->sum('amount');

        return [
            'total_receipt' => $totalReceipt,
            'total_payment' => $totalPayment,
            'balance' => $cashBalance,
            'cash_balance' => $cashMethodBalance,
            'bank_balance' => $bankMethodBalance,
        ];
    }
}
