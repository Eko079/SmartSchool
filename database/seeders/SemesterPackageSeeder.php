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
        // Paket SMA 2026/2027 Ganjil + Genap. SPP tetap lintas semester (bulanan).
        $packages = [
            ['code' => 'HRG-GJL', 'name' => 'Herregistrasi Ganjil 2026/2027', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Daftar ulang semester ganjil', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7],
            ['code' => 'LKS-GJL', 'name' => 'LKS & Modul Semester Ganjil', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Paket LKS dan modul 1 semester', 'semester' => 'ganjil', 'due' => '2026-07-31', 'month' => 7],
            ['code' => 'LAB-GJL', 'name' => 'Praktikum Lab & CBT Ganjil', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Dana praktikum dan ujian CBT', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8],
            ['code' => 'EKS-GJL', 'name' => 'Ekskul & OSIS Ganjil', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Iuran ekskul dan OSIS per semester', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8],
            ['code' => 'PTS-GJL', 'name' => 'Ujian PTS/STS Ganjil', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Biaya ujian tengah semester', 'semester' => 'ganjil', 'due' => '2026-09-30', 'month' => 9],
            ['code' => 'PAS-GJL', 'name' => 'Ujian PAS/SAS Ganjil', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Biaya ujian akhir semester', 'semester' => 'ganjil', 'due' => '2026-11-30', 'month' => 11],
            ['code' => 'HRG-GNP', 'name' => 'Herregistrasi Genap 2026/2027', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Daftar ulang semester genap', 'semester' => 'genap', 'due' => '2027-01-15', 'month' => 1],
            ['code' => 'LKS-GNP', 'name' => 'LKS & Modul Semester Genap', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Paket LKS dan modul 1 semester', 'semester' => 'genap', 'due' => '2027-01-31', 'month' => 1],
            ['code' => 'LAB-GNP', 'name' => 'Praktikum Lab & CBT Genap', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Dana praktikum dan ujian CBT', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2],
            ['code' => 'EKS-GNP', 'name' => 'Ekskul & OSIS Genap', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Iuran ekskul dan OSIS per semester', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2],
            ['code' => 'PTS-GNP', 'name' => 'Ujian PTS/STS Genap', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Biaya ujian tengah semester', 'semester' => 'genap', 'due' => '2027-03-30', 'month' => 3],
            ['code' => 'PAS-GNP', 'name' => 'Ujian PAS/SAS Genap', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Biaya ujian akhir semester', 'semester' => 'genap', 'due' => '2027-05-31', 'month' => 5],
            ['code' => 'PSAJ-GNP', 'name' => 'PSAJ / UKK Kelas XII Genap', 'type' => 'semesteran', 'default_amount' => 750000, 'description' => 'Ujian akhir jenjang khusus kelas XII', 'semester' => 'genap', 'due' => '2027-03-31', 'month' => 3],
        ];

        $cats = [];
        foreach ($packages as $p) {
            $cats[$p['code']] = FeeCategory::updateOrCreate(['code' => $p['code']], [
                'name' => $p['name'], 'type' => $p['type'], 'default_amount' => $p['default_amount'],
                'description' => $p['description'], 'is_active' => true,
            ]);
        }

        $students = Student::where('status', 'aktif')->get();
        foreach ($students as $st) {
            foreach ($packages as $p) {
                $tag = $p['semester'] === 'ganjil' ? 'GJL' : 'GNP';
                $year = $p['semester'] === 'ganjil' ? 2026 : 2027;
                $billCode = 'INV-' . $year . '-' . $tag . '-' . substr($p['code'], 0, 3) . '-' . str_pad($st->id, 4, '0', STR_PAD_LEFT);
                Bill::updateOrCreate(
                    ['bill_code' => $billCode],
                    [
                        'student_id' => $st->id,
                        'fee_category_id' => $cats[$p['code']]->id,
                        'period_month' => $p['month'],
                        'period_year' => $year,
                        'academic_year' => '2026/2027',
                        'semester' => $p['semester'],
                        'amount' => $p['default_amount'],
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                        'due_date' => Carbon::parse($p['due']),
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
