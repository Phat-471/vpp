<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\StaffLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_account_can_use_email_or_phone_including_country_prefix(): void
    {
        $user = User::factory()->create(['role' => 'cashier', 'phone' => '0987.654.321']);
        foreach ([strtoupper($user->email), '0987654321', '0987 654 321', '+84 987 654 321'] as $identifier) {
            $this->post('/pos/login', ['identifier' => $identifier, 'password' => 'password'])->assertRedirect(route('pos.index'));
            $this->assertAuthenticatedAs($user);
            $this->post('/pos/logout');
        }
    }

    public function test_duplicate_staff_phone_is_rejected_instead_of_selecting_an_account(): void
    {
        User::factory()->create(['role' => 'cashier', 'phone' => '0987654321']);
        User::factory()->create(['role' => 'cashier', 'phone' => '0987.654.321']);
        $this->post('/pos/login', ['identifier' => '0987654321', 'password' => 'password'])->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_phone_login_still_requires_the_correct_password(): void
    {
        User::factory()->create(['role' => 'cashier', 'phone' => '0987654321']);
        $this->post('/pos/login', ['identifier' => '0987654321', 'password' => 'wrong-password'])->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_phone_formatting_does_not_bypass_the_login_throttle_key(): void
    {
        $login = app(StaffLogin::class);
        $this->assertSame($login->throttleKey('0987654321'), $login->throttleKey('0987.654.321'));
        $this->assertSame($login->throttleKey('0987654321'), $login->throttleKey('+84 987 654 321'));
    }

    public function test_legacy_admin_pos_url_opens_the_separate_pos_screen(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/pos-page')->assertRedirect(route('pos.index'));
    }
}
