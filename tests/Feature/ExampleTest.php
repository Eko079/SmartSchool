<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_admin_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_dashboard_renders(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali,');
    }


    public function test_admin_siswa_renders(): void
    {
        $response = $this->get(route('admin.siswa'));
        $response->assertStatus(200);
        $response->assertSee('Data Siswa');
    }

    public function test_admin_kategori_tagihan_renders(): void
    {
        $response = $this->get(route('admin.kategori-tagihan'));
        $response->assertStatus(200);
        $response->assertSee('Kategori Tagihan');
    }

    public function test_admin_billing_generator_renders(): void
    {
        $response = $this->get(route('admin.billing'));
        $response->assertStatus(200);
        $response->assertSee('Billing Generator');
    }

    public function test_admin_laporan_renders(): void
    {
        $response = $this->get(route('admin.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Pembayaran');
    }


    public function test_admin_pengaturan_renders(): void
    {
        $response = $this->get(route('admin.pengaturan'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan');
    }

    public function test_admin_bantuan_renders(): void
    {
        $response = $this->get(route('admin.bantuan'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Bantuan');
    }


    public function test_auth_pages_render(): void
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('password.request'))->assertStatus(200);
    }
}
