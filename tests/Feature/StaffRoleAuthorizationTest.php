<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffRoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_users_management(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('filament.admin.resources.users.index'));

        $response->assertSuccessful();
    }

    public function test_cashier_cannot_access_users_management(): void
    {
        $cashier = User::where('role', 'cashier')->first() ?? User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($cashier)->get(route('filament.admin.resources.users.index'));

        $response->assertForbidden();
    }

    public function test_technician_cannot_access_financial_report(): void
    {
        $technician = User::where('role', 'technician')->first() ?? User::factory()->create(['role' => 'technician']);

        $response = $this->actingAs($technician)->get(route('filament.admin.pages.financial-report-page'));

        $response->assertForbidden();
    }

    public function test_technician_cannot_access_orders(): void
    {
        $technician = User::where('role', 'technician')->first() ?? User::factory()->create(['role' => 'technician']);

        $response = $this->actingAs($technician)->get(route('filament.admin.resources.orders.index'));

        $response->assertForbidden();
    }

    public function test_cashier_can_access_orders(): void
    {
        $cashier = User::where('role', 'cashier')->first() ?? User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($cashier)->get(route('filament.admin.resources.orders.index'));

        $response->assertSuccessful();
    }

    public function test_cashier_cannot_delete_orders(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $admin = User::factory()->create(['role' => 'admin']);

        $order = new \App\Models\Order();

        $this->actingAs($cashier);
        $this->assertFalse(\App\Filament\Resources\OrderResource::canDelete($order));
        $this->assertFalse(\App\Filament\Resources\OrderResource::canDeleteAny());

        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\OrderResource::canDelete($order));
        $this->assertTrue(\App\Filament\Resources\OrderResource::canDeleteAny());
    }

    public function test_cashier_cannot_delete_products_or_customers(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $admin = User::factory()->create(['role' => 'admin']);

        $product = new \App\Models\Product();
        $customer = new \App\Models\Customer();

        $this->actingAs($cashier);
        $this->assertFalse(\App\Filament\Resources\ProductResource::canDelete($product));
        $this->assertFalse(\App\Filament\Resources\ProductResource::canDeleteAny());
        $this->assertFalse(\App\Filament\Resources\CustomerResource::canDelete($customer));
        $this->assertFalse(\App\Filament\Resources\CustomerResource::canDeleteAny());

        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\ProductResource::canDelete($product));
        $this->assertTrue(\App\Filament\Resources\ProductResource::canDeleteAny());
        $this->assertTrue(\App\Filament\Resources\CustomerResource::canDelete($customer));
        $this->assertTrue(\App\Filament\Resources\CustomerResource::canDeleteAny());
    }

    public function test_technician_cannot_delete_repair_tickets(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $admin = User::factory()->create(['role' => 'admin']);

        $ticket = new \App\Models\RepairTicket();

        $this->actingAs($technician);
        $this->assertFalse(\App\Filament\Resources\RepairTicketResource::canDelete($ticket));
        $this->assertFalse(\App\Filament\Resources\RepairTicketResource::canDeleteAny());

        $this->actingAs($cashier);
        $this->assertFalse(\App\Filament\Resources\RepairTicketResource::canDelete($ticket));
        $this->assertFalse(\App\Filament\Resources\RepairTicketResource::canDeleteAny());

        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\RepairTicketResource::canDelete($ticket));
        $this->assertTrue(\App\Filament\Resources\RepairTicketResource::canDeleteAny());
    }

    public function test_pos_shift_access_and_scoping(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $cashier1 = User::factory()->create(['role' => 'cashier']);
        $cashier2 = User::factory()->create(['role' => 'cashier']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($technician);
        $this->assertFalse(\App\Filament\Resources\PosShiftResource::canViewAny());

        $this->actingAs($cashier1);
        $this->assertTrue(\App\Filament\Resources\PosShiftResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\PosShiftResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\PosShiftResource::canDelete(new \App\Models\PosShift()));

        $shift1 = \App\Models\PosShift::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'cashier_id' => $cashier1->id,
            'status' => 'open',
            'opened_at' => now(),
            'opening_cash' => 1000000,
        ]);

        $shift2 = \App\Models\PosShift::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'cashier_id' => $cashier2->id,
            'status' => 'open',
            'opened_at' => now(),
            'opening_cash' => 500000,
        ]);

        // Cashier 1 only sees their own shift
        $this->actingAs($cashier1);
        $cashier1ShiftIds = \App\Filament\Resources\PosShiftResource::getEloquentQuery()->pluck('id')->all();
        $this->assertContains($shift1->id, $cashier1ShiftIds);
        $this->assertNotContains($shift2->id, $cashier1ShiftIds);

        // Admin sees all shifts
        $this->actingAs($admin);
        $adminShiftIds = \App\Filament\Resources\PosShiftResource::getEloquentQuery()->pluck('id')->all();
        $this->assertContains($shift1->id, $adminShiftIds);
        $this->assertContains($shift2->id, $adminShiftIds);
    }
}
