<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\RepairItem;
use App\Models\RepairTicket;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RepairInventorySyncTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): Customer
    {
        return Customer::create([
            'name' => 'Công ty TNHH Hoàng Long',
            'phone' => '0988776655',
            'phone_last4' => '6655',
            'address' => '456 Lê Duẩn, Hà Nội',
        ]);
    }

    private function createProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'sku' => 'PART-' . Str::random(5),
            'name' => 'Trống in Drum Canon 2900 / 303',
            'slug' => 'trong-in-drum-canon-2900-' . Str::random(5),
            'base_unit' => 'Cái',
            'retail_price' => 120000,
            'cost_price' => 60000,
            'stock_quantity' => 20,
            'is_service_part' => true,
            'is_active' => true,
        ], $attributes));
    }

    private function createTicket(array $attributes = []): RepairTicket
    {
        $customer = $this->createCustomer();

        return RepairTicket::create(array_merge([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'device_name' => 'Canon LBP 2900',
            'issue_description' => 'Bản in bị vệt đen dọc trang giấy, mòn trống',
            'status' => 'received',
            'labor_fee' => 50000,
            'payment_status' => 'unpaid',
        ], $attributes));
    }

    public function test_adding_repair_item_deducts_product_stock(): void
    {
        $part = $this->createProduct(['stock_quantity' => 15]);
        $ticket = $this->createTicket();

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 2,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 240000,
        ]);

        $this->assertSame(13, $part->fresh()->stock_quantity);
    }

    public function test_deleting_repair_item_refunds_stock_to_catalog(): void
    {
        $part = $this->createProduct(['stock_quantity' => 15]);
        $ticket = $this->createTicket();

        $item = RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 2,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 240000,
        ]);

        $this->assertSame(13, $part->fresh()->stock_quantity);

        $item->delete();

        $this->assertSame(15, $part->fresh()->stock_quantity);
    }

    public function test_updating_repair_item_quantity_adjusts_stock_appropriately(): void
    {
        $part = $this->createProduct(['stock_quantity' => 20]);
        $ticket = $this->createTicket();

        $item = RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 1,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 120000,
        ]);

        $this->assertSame(19, $part->fresh()->stock_quantity);

        // Tăng số lượng lên 3 -> trừ thêm 2 cái nữa
        $item->update(['quantity' => 3]);
        $this->assertSame(17, $part->fresh()->stock_quantity);

        // Giảm số lượng về 2 -> hoàn lại 1 cái
        $item->update(['quantity' => 2]);
        $this->assertSame(18, $part->fresh()->stock_quantity);
    }

    public function test_updating_repair_item_product_reconciles_both_products(): void
    {
        $partA = $this->createProduct(['name' => 'Linh kiện A', 'stock_quantity' => 10]);
        $partB = $this->createProduct(['name' => 'Linh kiện B', 'stock_quantity' => 10]);
        $ticket = $this->createTicket();

        $item = RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $partA->id,
            'item_name' => $partA->name,
            'unit' => $partA->base_unit,
            'quantity' => 2,
            'cost_price' => $partA->cost_price,
            'unit_price' => $partA->retail_price,
            'subtotal' => 240000,
        ]);

        $this->assertSame(8, $partA->fresh()->stock_quantity);
        $this->assertSame(10, $partB->fresh()->stock_quantity);

        // Đổi linh kiện sang Part B
        $item->update([
            'product_id' => $partB->id,
            'item_name' => $partB->name,
            'quantity' => 2,
        ]);

        // Part A được hoàn trả về 10, Part B bị trừ 2 còn 8
        $this->assertSame(10, $partA->fresh()->stock_quantity);
        $this->assertSame(8, $partB->fresh()->stock_quantity);
    }

    public function test_cancelling_repair_ticket_refunds_all_parts_stock(): void
    {
        $partA = $this->createProduct(['name' => 'Gạt mực lớn', 'stock_quantity' => 20]);
        $partB = $this->createProduct(['name' => 'Bao lụa sấy', 'stock_quantity' => 10]);
        $ticket = $this->createTicket();

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $partA->id,
            'item_name' => $partA->name,
            'unit' => $partA->base_unit,
            'quantity' => 2,
            'cost_price' => 30000,
            'unit_price' => 60000,
            'subtotal' => 120000,
        ]);

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $partB->id,
            'item_name' => $partB->name,
            'unit' => $partB->base_unit,
            'quantity' => 1,
            'cost_price' => 50000,
            'unit_price' => 100000,
            'subtotal' => 100000,
        ]);

        $this->assertSame(18, $partA->fresh()->stock_quantity);
        $this->assertSame(9, $partB->fresh()->stock_quantity);

        // Hủy phiếu sửa chữa qua InventoryService
        $service = app(InventoryService::class);
        $service->updateTicketStatus($ticket, 'cancelled', ['internal_notes' => 'Khách mang máy về không sửa nữa']);

        $this->assertSame('cancelled', $ticket->fresh()->status);
        $this->assertSame(20, $partA->fresh()->stock_quantity);
        $this->assertSame(10, $partB->fresh()->stock_quantity);
    }

    public function test_reactivating_cancelled_ticket_rededucts_all_parts_stock(): void
    {
        $part = $this->createProduct(['stock_quantity' => 20]);
        $ticket = $this->createTicket();

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 3,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 360000,
        ]);

        $this->assertSame(17, $part->fresh()->stock_quantity);

        // Hủy phiếu -> hoàn kho
        $ticket->update(['status' => 'cancelled']);
        $this->assertSame(20, $part->fresh()->stock_quantity);

        // Khách quay lại đồng ý sửa -> chuyển sang in_progress
        $ticket->update(['status' => 'in_progress']);
        $this->assertSame(17, $part->fresh()->stock_quantity);
    }

    public function test_modifying_cancelled_ticket_does_not_double_refund(): void
    {
        $part = $this->createProduct(['stock_quantity' => 20]);
        $ticket = $this->createTicket();

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 2,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 240000,
        ]);

        $this->assertSame(18, $part->fresh()->stock_quantity);

        // Hủy phiếu lần đầu
        $ticket->update(['status' => 'cancelled']);
        $this->assertSame(20, $part->fresh()->stock_quantity);

        // Cập nhật trường khác khi vẫn ở trạng thái cancelled
        $ticket->update(['internal_notes' => 'Khách xác nhận hủy qua Zalo']);
        $this->assertSame(20, $part->fresh()->stock_quantity);
    }

    public function test_deleting_repair_item_on_cancelled_ticket_does_not_double_refund(): void
    {
        $part = $this->createProduct(['stock_quantity' => 20]);
        $ticket = $this->createTicket();

        $item = RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 2,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 240000,
        ]);

        $this->assertSame(18, $part->fresh()->stock_quantity);

        // Hủy phiếu -> hoàn kho về 20
        $ticket->update(['status' => 'cancelled']);
        $this->assertSame(20, $part->fresh()->stock_quantity);

        // Xóa item trong phiếu đã hủy
        $item->delete();
        $this->assertSame(20, $part->fresh()->stock_quantity);
    }

    public function test_adding_repair_item_on_cancelled_ticket_does_not_deduct_stock(): void
    {
        $part = $this->createProduct(['stock_quantity' => 20]);
        $ticket = $this->createTicket(['status' => 'cancelled']);

        RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 2,
            'cost_price' => $part->cost_price,
            'unit_price' => $part->retail_price,
            'subtotal' => 240000,
        ]);

        // Do phiếu đang cancelled, việc thêm item không trừ kho
        $this->assertSame(20, $part->fresh()->stock_quantity);
    }

    public function test_check_ticket_stock_availability(): void
    {
        $partA = $this->createProduct(['name' => 'Trục từ', 'stock_quantity' => 5]);
        $ticket = $this->createTicket();

        $item = RepairItem::create([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $partA->id,
            'item_name' => $partA->name,
            'unit' => $partA->base_unit,
            'quantity' => 2,
            'cost_price' => $partA->cost_price,
            'unit_price' => $partA->retail_price,
            'subtotal' => 240000,
        ]);

        $service = app(InventoryService::class);
        $check = $service->checkTicketStockAvailability($ticket);
        $this->assertTrue($check['is_available']);
        $this->assertEmpty($check['shortages']);

        // Tăng số lượng lên 10 vượt quá tồn kho (hiện còn 3)
        $item->update(['quantity' => 10]);
        $checkShortage = $service->checkTicketStockAvailability($ticket);
        $this->assertFalse($checkShortage['is_available']);
        $this->assertCount(1, $checkShortage['shortages']);
        $this->assertSame($partA->id, $checkShortage['shortages'][0]['product_id']);
    }

    public function test_strict_deduct_ticket_stock_throws_validation_exception_on_shortage(): void
    {
        $part = $this->createProduct(['stock_quantity' => 1]);
        $ticket = $this->createTicket();

        // Tạo item trực tiếp bỏ qua hook để giả lập thiếu kho
        $item = new RepairItem([
            'repair_ticket_id' => $ticket->id,
            'product_id' => $part->id,
            'item_name' => $part->name,
            'unit' => $part->base_unit,
            'quantity' => 5,
            'cost_price' => 50000,
            'unit_price' => 100000,
            'subtotal' => 500000,
        ]);
        $item->saveQuietly();

        $service = app(InventoryService::class);

        $this->expectException(ValidationException::class);
        $service->deductTicketStock($ticket, strictCheck: true);
    }
}
