<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\FeeCategory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SemesterPackageSeeder extends Seeder
{
    public function run(): void
    {
        // Paket 1 semester SMA 2026/2027 Ganjil. SPP tetap lintas semester (bulanan).
        $packages = [
            ['code' => 'HRG-GJL', 'name' => 'Herregistrasi Ganjil 2026/2027', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Daftar ulang semester ganjil'],
            ['code' => 'LKS-GJL', 'name' => 'LKS & Modul Semester Ganjil', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Paket LKS dan modul 1 semester'],
            ['code' => 'LAB-GJL', 'name' => 'Praktikum Lab & CBT Ganjil', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Dana praktikum dan ujian CBT'],
            ['code' => 'EKS-GJL', 'name' => 'Ekskul & OSIS Ganjil', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Iuran ekskul dan OSIS per semester'],
            ['code' => 'PTS-GJL', 'name' => 'Ujian PTS/STS Ganjil', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Biaya ujian tengah semester'],
            ['code' => 'PAS-GJL', 'name' => 'Ujian PAS/SAS Ganjil', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Biaya ujian akhir semester'],
        ];

        $cats = [];
        foreach ($packages as $p) {
            $cats[$p['code']] = FeeCategory::updateOrCreate(['code' => $p['code']], array_merge($p, ['is_active' => true]));
        }

        // Deadline paket ganjil.
        $dueDates = [
            'HRG-GJL' => '2026-07-15',
            'LKS-GJL' => '2026-07-31',
            'LAB-GJL' => '2026-08-31',
            'EKS-GJL' => '2026-08-31',
            'PTS-GJL' => '2026-09-30',
            'PAS-GJL' => '2026-11-30',
        ];

        $students = Student::where('status', 'aktif')->get();
        foreach ($students as $st) {
            foreach ($cats as $code => $cat) {
                $billCode = 'INV-2026-GJL-' . substr($code, 0, 3) . '-' . str_pad($st->id, 4, '0', STR_PAD_LEFT);
                Bill::updateOrCreate(
                    ['bill_code' => $billCode],
                    [
                        'student_id' => $st->id,
                        'fee_category_id' => $cat->id,
                        'period_month' => 7,
                        'period_year' => 2026,
                        'academic_year' => '2026/2027',
                        'semester' => 'ganjil',
                        'amount' => $cat->default_amount,
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                        'due_date' => Carbon::parse($dueDates[$code]),
                    ]
                );
            }
        }

        // Tandai tagihan lama tanpa TA sebagai lintas-semester SPP bila kategorinya bulanan.
        Bill::whereNull('academic_year')
            ->whereHas('feeCategory', fn ($q) => $q->where('type', 'bulanan'))
            ->update(['academic_year' => '2026/2027', 'semester' => 'ganjil']);
    }
}
