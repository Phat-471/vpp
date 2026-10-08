<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class PosCart
{
    public function quote(array $items, int $discount = 0, bool $lock = false): array
    {
        Validator::make(['items' => array_values($items), 'discount' => $discount], [
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_id' => ['required', 'integer', 'min:1'],
            'items.*.product_unit_id' => ['nullable', 'integer', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'discount' => ['required', 'integer', 'min:0'],
        ], [], ['items' => 'giỏ hàng', 'discount' => 'giảm giá'])->validate();

        $query = Product::with('units')->whereIn('id', array_column($items, 'product_id'))->orderBy('id');
        if ($lock) {
            $query->lockForUpdate();
        }
        $products = $query->get()->keyBy('id');
        $lines = [];
        $requiredStock = [];
        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages(['cart' => 'Sản phẩm không còn được bán. Vui lòng kiểm tra lại giỏ hàng.']);
            }
            $unitId = $item['product_unit_id'] ?? null;
            $unit = $unitId ? $product->units->firstWhere('id', $unitId) : null;
            if ($unitId && ! $unit) {
                throw ValidationException::withMessages(['cart' => 'Đơn vị bán không thuộc sản phẩm đã chọn.']);
            }
            $rate = $unit ? (int) $unit->conversion_rate : 1;
            $price = (int) round((float) ($unit?->price ?? $product->retail_price));
            if ($rate < 1 || $price < 0) {
                throw ValidationException::withMessages(['cart' => 'Giá hoặc hệ số quy đổi của sản phẩm chưa hợp lệ.']);
            }
            $quantity = (int) $item['quantity'];
            $requiredStock[$product->id] = ($requiredStock[$product->id] ?? 0) + $quantity * $rate;
            $key = 'p_'.$product->id.'_'.($unitId ?: 'base');
            $combinedQuantity = ($lines[$key]['quantity'] ?? 0) + $quantity;
            $lines[$key] = [
                'product_id' => $product->id,
                'product_unit_id' => $unitId,
                'name' => $product->name,
                'unit_name' => $unit?->unit_name ?? $product->base_unit,
                'quantity' => $combinedQuantity,
                'conversion_rate' => $rate,
                'unit_price' => $price,
                'cost_price' => (int) round((float) $product->cost_price) * $rate,
                'subtotal' => $combinedQuantity * $price,
            ];
        }
        foreach ($requiredStock as $id => $quantity) {
            if ($quantity > $products[$id]->stock_quantity) {
                throw ValidationException::withMessages(['cart' => 'Không đủ tồn kho cho '.$products[$id]->name.'.']);
            }
        }
        $subtotal = array_sum(array_column($lines, 'subtotal'));
        if ($discount > $subtotal) {
            throw ValidationException::withMessages(['discount' => 'Giảm giá không được vượt quá tiền hàng.']);
        }

        return ['lines' => $lines, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => $subtotal - $discount];
    }
}
