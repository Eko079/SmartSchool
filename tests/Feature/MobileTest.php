<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MobileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::first() ?? User::factory()->create());
    }

    public function test_mobile_guest_redirects_to_login(): void
    {
        $this->post(route('logout'));
        foreach (['m.dashboard', 'm.siswa', 'm.tagihan', 'm.laporan', 'm.pengaturan', 'm.bantuan'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_mobile_pages_render_with_real_data(): void
    {
        $pages = [
            'm.dashboard' => 'Pembayaran Terbaru',
            'm.siswa' => 'Daftar Siswa',
            'm.tagihan' => 'Tagihan Terbaru',
            'm.laporan' => 'Pembayaran',
            'm.pengaturan' => 'Profil Sekolah',
            'm.bantuan' => 'FAQ Populer',
        ];

        foreach ($pages as $route => $text) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
            $response->assertSee($text);
        }
    }
}
