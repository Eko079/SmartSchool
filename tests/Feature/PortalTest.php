<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalTest extends TestCase
{
    protected function waliUser(): User
    {
        // Pakai wali eksisting (satu student = satu wali) agar login NIS tidak ambigu.
        $wali = User::where('role', 'wali')->firstOrFail();
        $wali->forceFill([
            'password' => Hash::make('wali123'),
            'failed_attempts' => 0,
            'locked_until' => null,
        ])->save();

        return $wali->fresh('student');
    }

    public function test_portal_guest_redirects_to_portal_login(): void
    {
        $this->get(route('portal.dashboard'))->assertRedirect(route('portal.login'));
        $this->get(route('portal.tagihan'))->assertRedirect(route('portal.login'));
    }

    public function test_portal_login_page_renders(): void
    {
        $this->get(route('portal.login'))->assertOk()->assertSee('Selamat Datang Kembali');
    }

    public function test_portal_pages_render_for_wali(): void
    {
        $wali = $this->waliUser();
        $this->actingAs($wali, 'wali');
        $this->actingAs($wali);

        $this->get(route('portal.dashboard'))->assertOk()->assertSee('Tagihan Terbaru');
        $this->get(route('portal.tagihan'))->assertOk()->assertSee('Tagihan Saya');
        $this->get(route('portal.riwayat'))->assertOk()->assertSee('Riwayat Pembayaran');
        $this->get(route('portal.profil'))->assertOk()->assertSee('Profil Anak');
        $this->get(route('portal.bantuan'))->assertOk()->assertSee('Pusat Bantuan');
        $this->get(route('portal.pengaturan'))->assertOk()->assertSee('Pengaturan Akun');
    }

    public function test_wali_can_login_with_nis(): void
    {
        $wali = $this->waliUser();
        $nis = $wali->student->nis;

        $response = $this->post(route('portal.login.attempt'), [
            'identifier' => $nis,
            'password' => 'wali123',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertAuthenticatedAs($wali, 'wali');
    }

    public function test_wali_cannot_access_admin(): void
    {
        $wali = $this->waliUser();
        $this->actingAs($wali);

        $this->get(route('admin.dashboard'))->assertRedirect(route('portal.dashboard'));
    }

    public function test_admin_cannot_access_portal(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'wali');

        $this->get(route('portal.dashboard'))->assertRedirect(route('portal.login'));
    }

    public function test_wali_sees_only_own_bills(): void
    {
        $wali = $this->waliUser();
        $this->actingAs($wali, 'wali');

        $response = $this->getJson(route('portal.tagihan'));
        $response->assertOk();

        $studentId = $wali->student_id;
        foreach ($response->json('bills.data') as $bill) {
            $this->assertEquals($studentId, Bill::find($bill['id'])->student_id);
        }
    }

    public function test_wali_lockout_after_5_failed_attempts(): void
    {
        $wali = $this->waliUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('portal.login.attempt'), [
                'identifier' => $wali->email,
                'password' => 'salah',
            ]);
        }

        $wali->refresh();
        $this->assertNotNull($wali->locked_until);

        $this->post(route('portal.login.attempt'), [
            'identifier' => $wali->email,
            'password' => 'wali123',
        ])->assertSessionHasErrors('identifier');

        $wali->forceFill(['locked_until' => null, 'failed_attempts' => 0])->save();
    }

    public function test_otp_request_and_reset_flow(): void
    {
        $wali = $this->waliUser();

        $otpResponse = $this->postJson(route('portal.otp'), ['identifier' => $wali->email]);
        $otpResponse->assertOk();
        $code = $otpResponse->json('debug_code');
        $this->assertNotEmpty($code);

        $reset = $this->post(route('portal.reset'), [
            'identifier' => $wali->email,
            'code' => $code,
            'password' => 'barusandi123',
            'password_confirmation' => 'barusandi123',
        ]);
        $reset->assertRedirect(route('portal.login'));

        $wali->forceFill(['password' => Hash::make('wali123')])->save();
    }

    public function test_pay_creates_va_and_callback_settles(): void
    {
        $wali = $this->waliUser();
        $this->actingAs($wali, 'wali');

        $bill = Bill::where('student_id', $wali->student_id)->where('status', '!=', 'paid')->first();
        if (! $bill) {
            $category = \App\Models\FeeCategory::firstOrFail();
            $bill = Bill::create([
                'bill_code' => 'INV-TEST-' . uniqid(),
                'order_id' => 'SS-INV-TEST-' . uniqid(),
                'student_id' => $wali->student_id,
                'fee_category_id' => $category->id,
                'period_month' => 10,
                'period_year' => 2026,
                'amount' => 850000,
                'paid_amount' => 0,
                'status' => 'unpaid',
                'due_date' => now()->addDays(7),
            ]);
        }
        $this->assertNotNull($bill, 'Butuh tagihan belum lunas untuk uji bayar.');

        $pay = $this->postJson(route('portal.tagihan.pay', $bill->id), ['method' => 'bca_va']);
        $pay->assertCreated();
        $this->assertNotEmpty($pay->json('transaction.va_number'));

        $orderId = $pay->json('transaction.order_id');
        $callback = $this->postJson(route('portal.midtrans.callback'), [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'gross_amount' => (string) $bill->amount,
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'TRX-TEST-001',
        ]);
        $callback->assertOk();

        $this->assertEquals('paid', $bill->fresh()->status);
    }
}
