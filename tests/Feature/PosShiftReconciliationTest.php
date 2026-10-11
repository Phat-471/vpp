<?php

namespace Tests\Feature;

use App\Actions\Pos\CreatePosOrder;
use App\Filament\Resources\PosShiftResource;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\User;
use App\Services\PosShiftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosShiftReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'cashier'): User
    {
        $user = User::factory()->create([
            'name' => 'Thu Ngân Mai Hương',
            'role' => $role,
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function sale(User $user, string $method = 'cash', int $amount = 50000): void
    {
        $product = Product::create([
            'sku' => Str::random(10),
            'slug' => (string) Str::uuid(),
            'name' => 'Tập giấy Bãi Bằng',
            'base_unit' => 'Ram',
            'retail_price' => $amount,
            'cost_price' => 30000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        app(CreatePosOrder::class)->handle($user, [
            ['product_id' => $product->id, 'product_unit_id' => null, 'quantity' => 1],
        ], [
            'customer_name' => 'Khách mua lẻ',
            'customer_phone' => '0901234567',
            'notes' => '',
            'discount' => 0,
            'cash_given' => $amount,
            'payment_method' => $method,
        ], (string) Str::uuid());
    }

    public function test_can_render_k80_shift_receipt_for_closed_shift_with_exact_match(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);

        // Mở ca với 100.000 ₫
        $shift = $service->open($user, 100000);

        // 1 đơn tiền mặt 50.000 ₫, 1 đơn VietQR 30.000 ₫
        $this->sale($user, 'cash', 50000);
        $this->sale($user, 'vietqr', 30000);

        // Đóng ca với 150.000 ₫ (Khớp tiền mặt: 100k đầu ca + 50k bán lẻ)
        $closed = $service->close($user, $shift->uuid, 150000, 'Ca khớp tiền chuẩn');

        // Test in theo id
        $response = $this->get(route('print.pos-shift', $closed->id));
        $response->assertOk();
        $response->assertSee('PHIẾU BÀN GIAO VÀ KẾT CA');
        $response->assertSee('ĐÃ CHỐT ĐÓNG CA');
        $response->assertSee('Thu Ngân Mai Hương');
        $response->assertSee('100.000'); // Tiền đầu ca
        $response->assertSee('50.000');  // Tiền mặt thu được
        $response->assertSee('80.000');  // Tổng doanh số (50k + 30k)
        $response->assertSee('150.000'); // Tiền đếm
        $response->assertSee('KHỚP TIỀN');

        // Test in theo uuid
        $responseUuid = $this->get(route('print.pos-shift', $closed->uuid));
        $responseUuid->assertOk();
        $responseUuid->assertSee('PHIẾU BÀN GIAO VÀ KẾT CA');
    }

    public function test_can_render_k80_shift_receipt_with_shortage_discrepancy(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);

        $shift = $service->open($user, 200000);
        $this->sale($user, 'cash', 100000);

        // Dự kiến: 300.000 ₫. Đếm thực tế: 290.000 ₫ -> Thiếu 10.000 ₫
        $closed = $service->close($user, $shift->uuid, 290000, 'Thối nhầm tiền cho khách đơn lẻ');

        $response = $this->get(route('print.pos-shift', $closed->id));
        $response->assertOk();
        $response->assertSee('THIẾU');
        $response->assertSee('Thối nhầm tiền cho khách đơn lẻ');
    }

    public function test_can_render_k80_shift_receipt_for_open_shift(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);

        $shift = $service->open($user, 100000);
        $this->sale($user, 'cash', 40000);

        $response = $this->get(route('print.pos-shift', $shift->id));
        $response->assertOk();
        $response->assertSee('CA ĐANG MỞ (TẠM TÍNH)');
        $response->assertSee('Thu Ngân Mai Hương');
        $response->assertSee('140.000'); // expected_cash hiện tại
    }

    public function test_filament_pos_shift_resource_index_page(): void
    {
        $user = $this->staff('admin');
        $service = app(PosShiftService::class);

        $shift = $service->open($user, 500000);
        $this->sale($user, 'cash', 80000);

        $response = $this->get(PosShiftResource::getUrl('index'));
        $response->assertOk();
        $response->assertSee('Ca thu ngân POS');
        $response->assertSee('Thu Ngân Mai Hương');
        $response->assertSee('500.000');
    }

    public function test_filament_pos_shift_resource_view_page(): void
    {
        $user = $this->staff('admin');
        $service = app(PosShiftService::class);

        $shift = $service->open($user, 500000);
        $service->close($user, $shift->uuid, 500000, 'Bàn giao ca sáng');

        $response = $this->get(PosShiftResource::getUrl('view', ['record' => $shift]));
        $response->assertOk();
        $response->assertSee('Thông tin ca làm việc');
        $response->assertSee('Đối soát tài chính');
        $response->assertSee('In phiếu kết ca K80');
        $this->assertSame('Bàn giao ca sáng', $shift->fresh()->notes);
    }
}
