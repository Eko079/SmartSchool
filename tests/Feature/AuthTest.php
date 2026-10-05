<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali');
    }

    public function test_login_branding_follows_school_settings(): void
    {
        \App\Models\SchoolSetting::set('school_name', 'SMK Branding Test');
        \App\Models\SchoolSetting::set('school_email', 'branding@test.sch.id');

        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('SMK Branding Test');
        $response->assertSee('branding@test.sch.id');
        $response->assertDontSee('Nusantara Plus');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@smartschool.id',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@smartschool.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@smartschool.id')->first();

        $response = $this->actingAs($user)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
