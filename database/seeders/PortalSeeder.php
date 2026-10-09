<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Faq;
use App\Models\FeeCategory;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori LAB (dipakai desain portal, belum ada di BillingSeeder)
        $lab = FeeCategory::updateOrCreate(
            ['code' => 'LAB-01'],
            [
                'name' => 'Biaya Praktikum Lab',
                'type' => 'bulanan',
                'default_amount' => 750000,
                'is_active' => true,
                'description' => 'Biaya praktikum laboratorium bulanan',
            ]
        );

        // 2. Akun wali demo (opsi A: users.role=wali + student_id)
        $students = Student::all();
        foreach ($students as $st) {
            $email = 'wali-' . $st->nis . '@smartschool.id';
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $st->guardian_name ?: ('Wali ' . $st->name),
                    'password' => Hash::make('wali123'),
                    'role' => 'wali',
                    'student_id' => $st->id,
                    'phone' => $st->guardian_phone,
                    'failed_attempts' => 0,
                    'locked_until' => null,
                    'notify_wa' => true,
                    'notify_email' => true,
                ]
            );

            // Lengkapi kolom portal students bila kosong
            if (empty($st->email)) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $st->name));
                $st->email = $slug . '.' . $st->nis . '@smartschool.id';
            }
            if (empty($st->entry_year)) {
                $st->entry_year = 2023;
            }
            $st->save();
        }

        // 3. Bills multi-periode untuk 3 siswa pertama (simulasi desain: 6-7 tagihan)
        $spp = FeeCategory::whereIn('code', ['SPP', 'SPP-2026'])->first();
        $extraPeriods = [
            ['month' => 8, 'year' => 2026, 'category' => $lab, 'amount' => 750000, 'status' => 'paid'],
            ['month' => 7, 'year' => 2026, 'category' => $spp, 'amount' => 1200000, 'status' => 'unpaid'],
            ['month' => 6, 'year' => 2026, 'category' => $spp, 'amount' => 850000, 'status' => 'overdue'],
            ['month' => 5, 'year' => 2026, 'category' => $spp, 'amount' => 450000, 'status' => 'paid'],
            ['month' => 4, 'year' => 2026, 'category' => $spp, 'amount' => 850000, 'status' => 'paid'],
        ];
        foreach ($students->take(3) as $idx => $st) {
            foreach ($extraPeriods as $p) {
                $cat = $p['category'] ?? $spp;
                if (! $cat) {
                    continue;
                }
                $billCode = sprintf('INV-%d%02d-%03d', $p['year'], $p['month'], $st->id);
                $paid = $p['status'] === 'paid' ? $p['amount'] : 0;
                Bill::updateOrCreate(
                    ['bill_code' => $billCode],
                    [
                        'order_id' => 'SS-' . $billCode,
                        'student_id' => $st->id,
                        'fee_category_id' => $cat->id,
                        'period_month' => $p['month'],
                        'period_year' => $p['year'],
                        'amount' => $p['amount'],
                        'paid_amount' => $paid,
                        'fine_amount' => $p['status'] === 'overdue' ? 25000 : 0,
                        'status' => $p['status'],
                        'due_date' => Carbon::create($p['year'], $p['month'], 12),
                    ]
                );
            }
        }

        // 4. FAQ awal dari desain portal
        $faqs = [
            ['category' => 'pembayaran', 'question' => 'Bagaimana cara bayar tagihan?', 'answer' => 'Buka Tagihan Saya, klik Bayar, pilih VA/QRIS, simpan kuitansi.', 'sort_order' => 1],
            ['category' => 'pembayaran', 'question' => 'Tagihan belum lunas?', 'answer' => 'Buka Tagihan Saya, klik Bayar, pilih VA/QRIS untuk melunasi.', 'sort_order' => 2],
            ['category' => 'kuitansi', 'question' => 'Cara unduh kuitansi pembayaran?', 'answer' => 'Buka Riwayat Bayar, pilih transaksi lunas, klik Unduh Kuitansi.', 'sort_order' => 3],
            ['category' => 'laporan', 'question' => 'Cara unduh laporan pembayaran?', 'answer' => 'Buka Laporan, pilih periode, lalu Export PDF / Excel.', 'sort_order' => 4],
        ];
        foreach ($faqs as $f) {
            Faq::updateOrCreate(
                ['question' => $f['question']],
                array_merge($f, ['is_active' => true])
            );
        }

        // 5. Settings keys baru portal (tanpa migrasi)
        $portalSettings = [
            'logo_path' => 'logo-sekolah.png',
            'primary_color' => '#2563EB',
            'due_default_days' => '14',
            'fine_per_day' => '25000',
            'fine_max' => '250000',
            'wa_enabled' => '1',
            'support_hours' => '08.00-17.00',
            'support_whatsapp' => '(021) 5090-1234',
            'google_client_id' => '',
        ];
        foreach ($portalSettings as $k => $v) {
            SchoolSetting::set($k, $v);
        }
    }
}
