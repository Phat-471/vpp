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
}
