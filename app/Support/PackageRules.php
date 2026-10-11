<?php

namespace App\Support;

/** Aturan paket tagihan SMA: umum + khusus jurusan + kondisional tingkat. */
class PackageRules
{
    /** majors: null = semua jurusan; grades: null = semua tingkat. */
    public static function packages(): array
    {
        return [
            ['code' => 'DU-GJL', 'name' => 'Daftar Ulang', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Administrasi pendataan awal semester ganjil', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => null],
            ['code' => 'LKS-GJL', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'ganjil', 'due' => '2026-07-31', 'month' => 7, 'majors' => null, 'grades' => null],
            ['code' => 'UJI-GJL', 'name' => 'Ujian Semester', 'type' => 'semesteran', 'default_amount' => 700000, 'description' => 'Iuran PTS dan PAS digabung', 'semester' => 'ganjil', 'due' => '2026-11-30', 'month' => 11, 'majors' => null, 'grades' => null],
            ['code' => 'KOM-GJL', 'name' => 'Iuran Komite / OSIS', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Dukungan ekstrakurikuler dan acara sekolah', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => null, 'grades' => null],
            ['code' => 'LKS-GNP', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'genap', 'due' => '2027-01-31', 'month' => 1, 'majors' => null, 'grades' => null],
            ['code' => 'UJI-GNP', 'name' => 'Ujian Semester', 'type' => 'semesteran', 'default_amount' => 700000, 'description' => 'Iuran PTS dan PAS digabung', 'semester' => 'genap', 'due' => '2027-05-31', 'month' => 5, 'majors' => null, 'grades' => null],
            ['code' => 'KOM-GNP', 'name' => 'Iuran Komite / OSIS', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Dukungan ekstrakurikuler dan acara sekolah', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => null, 'grades' => null],
            ['code' => 'LAB-IPA-GJL', 'name' => 'Praktikum Laboratorium', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Bahan habis pakai kimia, biologi, fisika', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IPA'], 'grades' => null],
            ['code' => 'LAB-IPA-GNP', 'name' => 'Praktikum Laboratorium', 'type' => 'semesteran', 'default_amount' => 450000, 'description' => 'Bahan habis pakai kimia, biologi, fisika', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IPA'], 'grades' => null],
            ['code' => 'STD-IPS-GJL', 'name' => 'Studi Lapangan', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Kunjungan observasi museum/wilayah', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IPS'], 'grades' => null],
            ['code' => 'STD-IPS-GNP', 'name' => 'Studi Lapangan', 'type' => 'semesteran', 'default_amount' => 400000, 'description' => 'Kunjungan observasi museum/wilayah', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IPS'], 'grades' => null],
            ['code' => 'LAB-IBB-GJL', 'name' => 'Praktikum Bahasa', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Penggunaan dan perawatan lab bahasa', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'LAB-IBB-GNP', 'name' => 'Praktikum Bahasa', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Penggunaan dan perawatan lab bahasa', 'semester' => 'genap', 'due' => '2027-02-28', 'month' => 2, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'PEN-IBB-GJL', 'name' => 'Pentas Budaya', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Pagelaran seni, teater, festival bahasa', 'semester' => 'ganjil', 'due' => '2026-09-30', 'month' => 9, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'PEN-IBB-GNP', 'name' => 'Pentas Budaya', 'type' => 'semesteran', 'default_amount' => 300000, 'description' => 'Pagelaran seni, teater, festival bahasa', 'semester' => 'genap', 'due' => '2027-03-30', 'month' => 3, 'majors' => ['IBB'], 'grades' => null],
            ['code' => 'PSAJ-GNP', 'name' => 'PSAJ / UKK Kelas XII', 'type' => 'semesteran', 'default_amount' => 750000, 'description' => 'Ujian akhir jenjang khusus kelas XII', 'semester' => 'genap', 'due' => '2027-03-31', 'month' => 3, 'majors' => null, 'grades' => ['XII']],
            ['code' => 'SRG-GJL', 'name' => 'Paket Seragam Sekolah', 'type' => 'sekali', 'default_amount' => 1200000, 'description' => 'Paket seragam siswa baru kelas X', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => ['X']],
        ];
    }

    public static function ruleFor(string $code): ?array
    {
        foreach (self::packages() as $p) {
            if ($p['code'] === $code) {
                return $p;
            }
        }

        return null;
    }

    /** Kategori tanpa aturan (mis. SPP) berlaku untuk semua siswa. */
    public static function appliesTo(string $code, ?string $major, ?string $grade): bool
    {
        $rule = self::ruleFor($code);
        if (! $rule) {
            return true;
        }
        if ($rule['majors'] !== null && ! in_array($major, $rule['majors'], true)) {
            return false;
        }
        if ($rule['grades'] !== null && ! in_array($grade, $rule['grades'], true)) {
            return false;
        }

        return true;
    }
}
