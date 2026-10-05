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

    public function test_mobile_siswa_popup_stays_in_mobile(): void
    {
        $st = \App\Models\Student::first();

        // Popup detail: JSON tetap di /m, bukan /admin.
        $this->getJson(route('m.siswa.bills', $st->id))->assertStatus(200);

        // Popup edit: submit PUT kembali ke /m.siswa.
        $res = $this->put(route('m.siswa.update', $st->id), [
            'name' => $st->name,
            'nis' => $st->nis,
            'class_id' => $st->class_id,
            'gender' => $st->gender,
            'status' => $st->status,
        ]);
        $res->assertRedirect(route('m.siswa'));

        // Halaman siswa tidak ada lagi link lempar ke /admin.
        $html = $this->get(route('m.siswa'))->getContent();
        $this->assertStringNotContainsString('/admin/siswa/', $html);
        $this->assertStringNotContainsString("route('admin.", $html);
    }

    public function test_mobile_settings_popup_stays_in_mobile(): void
    {
        $res = $this->post(route('m.pengaturan.update'), [
            'school_name' => 'SMK Mobile Test',
            'academic_year' => '2026/2027',
            'active_semester' => 'Ganjil',
        ]);
        $res->assertRedirect(route('m.pengaturan'));
        $this->assertEquals('SMK Mobile Test', \App\Models\SchoolSetting::get('school_name'));

        // Kembalikan nama sekolah agar tidak ubah data nyata.
        \App\Models\SchoolSetting::set('school_name', 'SMA Nusantara Plus');
    }
}
