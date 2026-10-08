<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\StorefrontSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_settings_are_used_by_public_pages_and_contact_links(): void
    {
        foreach ([
            'site_name' => 'Văn phòng phẩm Kiểm thử',
            'site_slogan' => 'Văn phòng phẩm cho mọi văn phòng',
            'hotline' => '028.1234.5678',
            'zalo' => '0987.654.321',
            'address' => 'Địa chỉ cửa hàng kiểm thử',
            'opening_hours' => '08:00 - 17:00',
            'notice_bar_text' => 'Ưu đãi dành cho khách hàng mới',
            'freeship_threshold' => 750000,
        ] as $key => $value) {
            Setting::set($key, $value);
        }

        foreach (['/', '/san-pham', '/dang-nhap', '/tra-cuu'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('Văn phòng phẩm Kiểm thử')
                ->assertSee('Văn phòng phẩm cho mọi văn phòng')
                ->assertSee('Địa chỉ cửa hàng kiểm thử')
                ->assertSee('Ưu đãi dành cho khách hàng mới')
                ->assertSee('750.000 ₫')
                ->assertSee('href="tel:02812345678"', false)
                ->assertSee('href="https://zalo.me/0987654321"', false);
        }

        foreach (['/gioi-thieu', '/chinh-sach-mua-hang', '/chinh-sach-bao-mat'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('Văn phòng phẩm Kiểm thử')
                ->assertSee('Địa chỉ cửa hàng kiểm thử');
        }
    }

    public function test_saving_after_cache_is_warmed_updates_the_next_request(): void
    {
        Setting::set('site_name', 'Cửa hàng trước cập nhật');
        $this->get('/dang-nhap')->assertSee('Cửa hàng trước cập nhật');

        Setting::set('site_name', 'Cửa hàng sau cập nhật');
        $this->get('/dang-nhap')->assertOk()
            ->assertSee('Cửa hàng sau cập nhật')
            ->assertDontSee('Cửa hàng trước cập nhật');
    }

    public function test_store_content_is_escaped_and_private_settings_are_not_shared(): void
    {
        Setting::set('notice_bar_text', '<script>alert(1)</script>');
        Setting::set('private_test_key', 'private-test-value');

        $this->get('/dang-nhap')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('private-test-value');

        $this->assertArrayNotHasKey('private_test_key', app(StorefrontSettings::class)->all());
    }
}
