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
        foreach (['admin.dashboard', 'admin.siswa', 'admin.billing', 'admin.laporan', 'admin.pengaturan', 'admin.bantuan', 'admin.kategori-tagihan'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_mobile_pages_render_with_real_data(): void
    {
        $pages = [
            'admin.dashboard' => 'Pembayaran Terbaru',
            'admin.siswa' => 'Daftar Siswa',
            'admin.billing' => 'Tagihan Terbaru',
            'admin.laporan' => 'Pembayaran',
            'admin.pengaturan' => 'Profil Sekolah',
            'admin.bantuan' => 'FAQ Populer',
            'admin.kategori-tagihan' => 'KATEGORI',
        ];

        $phoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        foreach ($pages as $route => $text) {
            $response = $this->withHeaders(['User-Agent' => $phoneUa])->get(route($route));
            $response->assertStatus(200);
            $response->assertSee($text);
        }
    }

    public function test_mobile_siswa_popup_stays_in_mobile(): void
    {
        $st = \App\Models\Student::first();
        $phoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        // Popup detail: JSON via rute admin yang sama.
        $this->withHeaders(['User-Agent' => $phoneUa])->getJson(route('admin.siswa.bills', $st->id))->assertStatus(200);

        // Popup edit: submit PUT kembali ke halaman siswa.
        $res = $this->put(route('admin.siswa.update', $st->id), [
            'name' => $st->name,
            'nis' => $st->nis,
            'class_id' => $st->class_id,
            'gender' => $st->gender,
            'status' => $st->status,
        ]);
        $res->assertRedirect(route('admin.siswa'));

        // Halaman siswa versi HP pakai layout mobile.
        $html = $this->withHeaders(['User-Agent' => $phoneUa])->get(route('admin.siswa'))->getContent();
        $this->assertStringContainsString('m-stu-card', $html);
    }

    public function test_mobile_settings_popup_stays_in_mobile(): void
    {
        $res = $this->post(route('admin.pengaturan.update'), [
            'school_name' => 'SMK Mobile Test',
            'academic_year' => '2026/2027',
            'active_semester' => 'Ganjil',
        ]);
        $res->assertRedirect(route('admin.pengaturan'));
        $this->assertEquals('SMK Mobile Test', \App\Models\SchoolSetting::get('school_name'));

        // Kembalikan nama sekolah agar tidak ubah data nyata.
        \App\Models\SchoolSetting::set('school_name', 'SMA Nusantara Plus');
    }

    public function test_phone_gets_mobile_desktop_gets_desktop(): void
    {
        $phoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';
        $desktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';
        $tabletUa = 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        $phoneHtml = $this->withHeaders(['User-Agent' => $phoneUa])->get(route('admin.siswa'))->getContent();
        $this->assertStringContainsString('m-stu-card', $phoneHtml);

        $desktopHtml = $this->withHeaders(['User-Agent' => $desktopUa])->get(route('admin.siswa'))->getContent();
        $this->assertStringContainsString('ss-table', $desktopHtml);

        $tabletHtml = $this->withHeaders(['User-Agent' => $tabletUa])->get(route('admin.siswa'))->getContent();
        $this->assertStringContainsString('ss-table', $tabletHtml);
    }
}
