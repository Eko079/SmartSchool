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
        // Paket SMA 2026/2027: umum + khusus jurusan + kondisional.
        // majors: null = semua jurusan; grades: null = semua tingkat (X/XI/XII).
        $packages = [
            // 1. Umum — Daftar Ulang hanya Ganjil.
            ['code' => 'DU-GJL', 'name' => 'Daftar Ulang', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Administrasi pendataan awal semester ganjil', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => null],
            ['code' => 'LKS-GJL', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'ganjil', 'due' => '2026-07-31', 'month' => 7, 'majors' => null, 'grades' => null],
            ['code' => 'UJI-GJL', 'name' => 'Ujian Semester', 'type' => 'semesteran', 'default_amount' => 700000, 'description' => 'Iuran PTS dan PAS digabung', 'semester' => 'ganjil', 'due' => '2026-11-30', 'month' => 11, 'majors' => null, 'grades' => null],
            ['code' => 'KOM-GJL', 'name' => 'Iuran Komite / OSIS', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Dukungan ekstrakurikuler dan acara sekolah', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => null, 'grades' => null],
            ['code' => 'LKS-GNP', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'genap', 'due' => '2027-01-31', 'month' => 1, 'majors' => null, 'grades' => null],
            ['code' => 'UJI-GNP', 'name' => 'Ujian Semester', 'type' => 'semesteran', 'default_amount' => 700000, 'description' => 'Iuran PTS dan PAS digabung', 'semester' => 'genap', 'due' => '2027-05-31', 'month' => 5, 'majors' => null, 'grades' => null],
            ['code' => 'KOM-GNP', 'name' => 'Iuran Komite / OSIS', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Dukungan ekstrakurikuler dan acara sekolah', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => null, 'grades' => null],
            // 2. Khusus jurusan.
            ['code' => 'LAB-IPA-GJL', 'name' => 'Praktikum Laboratorium', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Bahan habis pakai kimia, biologi, fisika', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IPA'], 'grades' => null],
            ['code' => 'LAB-IPA-GNP', 'name' => 'Praktikum Laboratorium', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Bahan habis pakai kimia, biologi, fisika', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IPA'], 'grades' => null],
            ['code' => 'STD-IPS-GJL', 'name' => 'Studi Lapangan', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Kunjungan observasi museum/wilayah', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IPS'], 'grades' => null],
            ['code' => 'STD-IPS-GNP', 'name' => 'Studi Lapangan', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Kunjungan observasi museum/wilayah', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IPS'], 'grades' => null],
            ['code' => 'LAB-IBB-GJL', 'name' => 'Praktikum Bahasa', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Penggunaan dan perawatan lab bahasa', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'LAB-IBB-GNP', 'name' => 'Praktikum Bahasa', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Penggunaan dan perawatan lab bahasa', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'PEN-IBB-GJL', 'name' => 'Pentas Budaya', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Pagelaran seni, teater, festival bahasa', 'semester' => 'ganjil', 'due' => '2026-09-30', 'month' => 9, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'PEN-IBB-GNP', 'name' => 'Pentas Budaya', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Pagelaran seni, teater, festival bahasa', 'semester' => 'genap', 'due' => '2027-03-30', 'month' => 3, 'majors' => ['IBB'], 'grades' => null],
            // 4. PSAJ khusus XII Genap. 5. Seragam khusus X Ganjil.
            ['code' => 'PSAJ-GNP', 'name' => 'PSAJ / UKK Kelas XII', 'type' => 'semesteran', 'default_amount' => 750000, 'description' => 'Ujian akhir jenjang khusus kelas XII', 'semester' => 'genap', 'due' => '2027-03-31', 'month' => 3, 'majors' => null, 'grades' => ['XII']],
            ['code' => 'SRG-GJL', 'name' => 'Paket Seragam Sekolah', 'type' => 'sekali', 'default_amount' => 1200000, 'description' => 'Paket seragam siswa baru kelas X', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => ['X']],
        ];

        $cats = [];
        foreach ($packages as $p) {
            $cats[$p['code']] = FeeCategory::updateOrCreate(['code' => $p['code']], [
                'name' => $p['name'], 'type' => $p['type'], 'default_amount' => $p['default_amount'],
                'description' => $p['description'], 'is_active' => true,
            ]);
        }

        // Nonaktifkan kategori lama yang digantikan (tagihan lama tetap tersimpan).
        FeeCategory::whereIn('code', [
            'HRG-GJL', 'HRG-GNP', 'PTS-GJL', 'PAS-GJL', 'PTS-GNP', 'PAS-GNP',
            'LAB-GJL', 'LAB-GNP', 'EKS-GJL', 'EKS-GNP',
        ])->update(['is_active' => false]);

        $students = Student::with('classRoom')->where('status', 'aktif')->get();
        foreach ($students as $st) {
            $major = $st->classRoom->major ?? null;
            $grade = $st->classRoom->grade ?? null;
            foreach ($packages as $p) {
                // Daftar Ulang hanya Ganjil.
                if ($p['semester'] === 'genap' && str_starts_with($p['code'], 'DU-')) {
                    continue;
                }
                if ($p['majors'] !== null && ! in_array($major, $p['majors'], true)) {
                    continue;
                }
                if ($p['grades'] !== null && ! in_array($grade, $p['grades'], true)) {
                    continue;
                }
                $tag = $p['semester'] === 'ganjil' ? 'GJL' : 'GNP';
                $year = $p['semester'] === 'ganjil' ? 2026 : 2027;
                $billCode = 'INV-'.$year.'-'.$tag.'-'.substr(strtoupper(preg_replace('/[^A-Z0-9]/', '', $p['code'])), 0, 3).'-'.str_pad($st->id, 4, '0', STR_PAD_LEFT);
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
    }
}
