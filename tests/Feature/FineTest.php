<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\FeeCategory;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class FineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SchoolSetting::set('fine_per_day', '5000');
        SchoolSetting::set('fine_max', '50000');
        SchoolSetting::set('grace_days', '3');
    }

    protected function makeBill(array $over = []): Bill
    {
        $student = Student::first() ?? Student::factory()->create();
        $cat = FeeCategory::first() ?? FeeCategory::factory()->create();

        return Bill::create(array_merge([
            'bill_code' => 'FINE-' . rand(100000, 999999),
            'student_id' => $student->id,
            'fee_category_id' => $cat->id,
            'period_month' => 1,
            'period_year' => 2026,
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'amount' => 100000,
            'paid_amount' => 0,
            'fine_amount' => 0,
            'status' => 'unpaid',
            'due_date' => Carbon::today()->subDays(10)->format('Y-m-d'),
        ], $over));
    }

    public function test_calc_fine_respects_grace_and_max(): void
    {
        // 10 hari telat - 3 grace = 7 x 5000 = 35000.
        $calc = Bill::calcFine(Carbon::today()->subDays(10)->format('Y-m-d'));
        $this->assertEquals(10, $calc['days_late']);
        $this->assertEquals(35000, $calc['fine']);

        // Masih dalam grace: 0.
        $calc2 = Bill::calcFine(Carbon::today()->subDays(2)->format('Y-m-d'));
        $this->assertEquals(0, $calc2['fine']);

        // Cap max 50000: 30 hari -> 27 x 5000 = 135000 -> 50000.
        $calc3 = Bill::calcFine(Carbon::today()->subDays(30)->format('Y-m-d'));
        $this->assertEquals(50000, $calc3['fine']);
    }

    public function test_mark_overdue_sets_status_and_fine(): void
    {
        $bill = $this->makeBill();
        Artisan::call('bills:mark-overdue');
        $bill->refresh();

        $this->assertEquals('overdue', $bill->status);
        $this->assertEquals(35000, (float) $bill->fine_amount);
        $this->assertEquals(135000, $bill->total_due);
        $this->assertDatabaseHas('bill_events', ['bill_id' => $bill->id, 'event' => 'overdue']);
    }

    public function test_mark_overdue_idempotent_and_grace_safe(): void
    {
        $bill = $this->makeBill();
        Artisan::call('bills:mark-overdue');
        $firstEvents = \App\Models\BillEvent::where('bill_id', $bill->id)->where('event', 'overdue')->count();
        Artisan::call('bills:mark-overdue');
        $secondEvents = \App\Models\BillEvent::where('bill_id', $bill->id)->where('event', 'overdue')->count();
        $this->assertEquals($firstEvents, $secondEvents);

        // Dalam grace tidak jadi overdue.
        $fresh = $this->makeBill(['bill_code' => 'FINE-' . rand(100000, 999999), 'due_date' => Carbon::today()->subDays(1)->format('Y-m-d')]);
        Artisan::call('bills:mark-overdue');
        $fresh->refresh();
        $this->assertEquals('unpaid', $fresh->status);
        $this->assertEquals(0, (float) $fresh->fine_amount);
    }

    public function test_paid_bill_never_fined(): void
    {
        $bill = $this->makeBill(['status' => 'paid', 'paid_amount' => 100000]);
        Artisan::call('bills:mark-overdue');
        $bill->refresh();
        $this->assertEquals('paid', $bill->status);
        $this->assertEquals(0, (float) $bill->fine_amount);
    }

    public function test_callback_settles_principal_plus_fine(): void
    {
        $bill = $this->makeBill();
        Artisan::call('bills:mark-overdue');
        $bill->refresh();
        $total = $bill->total_due;

        $resp = $this->postJson(route('portal.midtrans.callback'), [
            'order_id' => 'SS-' . $bill->bill_code,
            'transaction_status' => 'settlement',
            'gross_amount' => $total,
            'payment_type' => 'bank_transfer',
        ]);
        // order_id auto = SS-bill_code bila belum ada transaksi pay.
        if ($resp->status() === 404) {
            $bill->forceFill(['order_id' => 'SS-' . $bill->bill_code])->save();
            $resp = $this->postJson(route('portal.midtrans.callback'), [
                'order_id' => 'SS-' . $bill->bill_code,
                'transaction_status' => 'settlement',
                'gross_amount' => $total,
                'payment_type' => 'bank_transfer',
            ]);
        }
        $resp->assertOk();
        $bill->refresh();
        $this->assertEquals('paid', $bill->status);
        $this->assertEquals($total, (float) \App\Models\Payment::where('bill_id', $bill->id)->where('status', 'success')->first()->amount);
    }

    public function test_admin_can_update_fine_settings(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('admin.pengaturan.update'), [
            'fine_per_day' => 10000,
            'fine_max' => 200000,
            'grace_days' => 5,
        ])->assertRedirect(route('admin.pengaturan'));

        $this->assertEquals('10000', SchoolSetting::get('fine_per_day'));
        $this->assertEquals('200000', SchoolSetting::get('fine_max'));
        $this->assertEquals('5', SchoolSetting::get('grace_days'));
    }
}
