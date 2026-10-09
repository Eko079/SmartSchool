<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use App\Models\Student;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            ['code' => 'SPP', 'name' => 'SPP Bulanan', 'type' => 'bulanan', 'default_amount' => 850000, 'description' => 'Iuran pembinaan pendidikan rutin setiap bulan'],
            ['code' => 'SRG-01', 'name' => 'Biaya Seragam Sekolah', 'type' => 'sekali', 'default_amount' => 1200000, 'description' => 'Paket 4 setel seragam resmi, olahraga & batik'],
            ['code' => 'GDG-01', 'name' => 'Uang Gedung & Sarpras', 'type' => 'bebas', 'default_amount' => 2500000, 'description' => 'Pemeliharaan fasilitas lab komputer dan pendingin ruangan'],
            ['code' => 'KEG-01', 'name' => 'Kegiatan & Ujian Semester', 'type' => 'bulanan', 'default_amount' => 450000, 'description' => 'Dana praktikum, PTS & PAS berbasis CBT'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['code']] = FeeCategory::updateOrCreate(['code' => $c['code']], $c);
        }

        $students = Student::all();
        $spp = $categories['SPP'];
        $methods = ['Tunai / Kasir', 'VA BCA', 'VA BNI', 'QRIS'];

        foreach ($students as $idx => $st) {
            $billCode = 'INV-202609-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
            $amount = 850000;

            if ($idx < 6) {
                $status = 'paid';
                $paidAmount = $amount;
            } elseif ($idx === 6 || $idx === 7) {
                $status = 'partial';
                $paidAmount = 500000;
            } elseif ($idx === 8 || $idx === 9) {
                $status = 'overdue';
                $paidAmount = 0;
            } else {
                $status = 'unpaid';
                $paidAmount = 0;
            }

            $bill = Bill::updateOrCreate(
                ['bill_code' => $billCode],
                [
                    'student_id' => $st->id,
                    'fee_category_id' => $spp->id,
                    'period_month' => 9,
                    'period_year' => 2026,
                    'amount' => $amount,
                    'paid_amount' => $paidAmount,
                    'status' => $status,
                    'due_date' => Carbon::create(2026, 9, 10),
                ]
            );

            if ($paidAmount > 0) {
                Payment::updateOrCreate(
                    ['invoice_number' => 'PAY-202609' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT)],
                    [
                        'bill_id' => $bill->id,
                        'student_id' => $st->id,
                        'amount' => $paidAmount,
                        'payment_method' => $methods[$idx % count($methods)],
                        'status' => 'success',
                        'paid_at' => Carbon::create(2026, 9, rand(1, 10), rand(8, 16), rand(10, 59)),
                        'note' => 'Pembayaran SPP Bulan September 2026',
                    ]
                );
            }
        }
    }
}

