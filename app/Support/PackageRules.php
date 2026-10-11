<?php

namespace App\Support;

/** Aturan paket tagihan SMA: umum + khusus jurusan + kondisional tingkat. */
class PackageRules
{
    /** majors: null = semua jurusan; grades: null = semua tingkat. */
    public static function packages(): array
    {
        return [
            ['code' => 'DU-GJL', 'name' => 'Daftar Ulang', 'type' => 'semesteran', 'default_amount' => 500000, 'description' => 'Administrasi pendataan awal semester ganjil', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => null, 'skip_new' => true],
            ['code' => 'LKS-GJL', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'ganjil', 'due' => '2026-07-31', 'month' => 7, 'majors' => null, 'grades' => null],
            ['code' => 'UTS-GJL', 'name' => 'UTS', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Ujian tengah semester ganjil', 'semester' => 'ganjil', 'due' => '2026-10-31', 'month' => 10, 'majors' => null, 'grades' => null],
            ['code' => 'UAS-GJL', 'name' => 'UAS', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Ujian akhir semester ganjil', 'semester' => 'ganjil', 'due' => '2026-12-10', 'month' => 12, 'majors' => null, 'grades' => null],
            ['code' => 'KOM-GJL', 'name' => 'Iuran Komite / OSIS', 'type' => 'semesteran', 'default_amount' => 200000, 'description' => 'Dukungan ekstrakurikuler dan acara sekolah', 'semester' => 'ganjil', 'due' => '2026-08-31', 'month' => 8, 'majors' => null, 'grades' => null],
            ['code' => 'LKS-GNP', 'name' => 'Buku LKS / Modul', 'type' => 'semesteran', 'default_amount' => 600000, 'description' => 'Buku kerja cetak pendamping 1 semester', 'semester' => 'genap', 'due' => '2027-01-31', 'month' => 1, 'majors' => null, 'grades' => null],
            ['code' => 'UTS-GNP', 'name' => 'UTS', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Ujian tengah semester genap', 'semester' => 'genap', 'due' => '2027-03-20', 'month' => 3, 'majors' => null, 'grades' => null],
            ['code' => 'UAS-GNP', 'name' => 'UAS', 'type' => 'semesteran', 'default_amount' => 350000, 'description' => 'Ujian akhir semester genap', 'semester' => 'genap', 'due' => '2027-05-30', 'month' => 5, 'majors' => null, 'grades' => null],
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
            ['code' => 'SRG-GJL', 'name' => 'Paket Seragam Sekolah', 'type' => 'sekali', 'default_amount' => 1200000, 'description' => 'Paket seragam siswa baru kelas X', 'semester' => 'ganjil', 'due' => '2026-07-15', 'month' => 7, 'majors' => null, 'grades' => ['X'], 'new_only' => true],
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
    public static function appliesTo(string $code, ?string $major, ?string $grade, ?int $entryYear = null, ?string $academicYear = null): bool
    {
        try {
            $cat = \App\Models\FeeCategory::where('code', $code)->first();
            if ($cat) {
                return self::appliesToCategory($cat, $major, $grade, $entryYear, $academicYear);
            }
        } catch (\Throwable $e) {
            // Tabel belum siap (migrasi) → pakai aturan bawaan di bawah.
        }
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
        // Aturan siswa baru: entry_year == tahun awal TA (mis. 2026 untuk 2026/2027).
        // Bila entry/TA tak diketahui, aturan baru diabaikan (perilaku lama) agar aman.
        $isNew = self::isNewStudent($entryYear, $academicYear);
        if ($isNew === true && ($rule['skip_new'] ?? false)) {
            return false;
        }
        if ($isNew === false && ($rule['new_only'] ?? false)) {
            return false;
        }

        return true;
    }

    /** Berlaku untuk siswa ini? Baca dari kolom database (null = semua). */
    public static function appliesToCategory(object $cat, ?string $major, ?string $grade, ?int $entryYear = null, ?string $academicYear = null): bool
    {
        $majors = $cat->majors ?? null;
        if (! empty($majors) && ! in_array($major, (array) $majors, true)) {
            return false;
        }
        $grades = $cat->grades ?? null;
        if (! empty($grades) && ! in_array($grade, (array) $grades, true)) {
            return false;
        }
        $isNew = self::isNewStudent($entryYear, $academicYear);
        if ($isNew === true && ($cat->audience ?? null) === 'lama') {
            return false;
        }
        if ($isNew === false && ($cat->audience ?? null) === 'baru') {
            return false;
        }

        return true;
    }

    /** Jatuh tempo dari kolom database; kosong = 2 minggu dari generate. */
    public static function dueDateFor(object $cat, string $academicYear, string $semester): \Carbon\Carbon
    {
        $month = $cat->due_month ?? null;
        $day = $cat->due_day ?? null;
        if ($month && $day) {
            $parts = explode('/', $academicYear);
            $year = $semester === 'ganjil' ? (int) ($parts[0] ?? date('Y')) : (int) ($parts[1] ?? date('Y'));
            $day = min((int) $day, \Carbon\Carbon::create($year, (int) $month, 1)->daysInMonth);

            return \Carbon\Carbon::create($year, (int) $month, $day);
        }

        return now()->addDays(14);
    }
    /** true = siswa baru (masuk di TA tsb), false = lama, null = tak diketahui. */
    public static function isNewStudent(?int $entryYear, ?string $academicYear): ?bool
    {
        if ($entryYear === null || $entryYear <= 0 || ! $academicYear || ! str_contains($academicYear, '/')) {
            return null;
        }

        return $entryYear === (int) explode('/', $academicYear)[0];
    }

    /** Kode kategori paket semester + SPP (daftar utama). Di luar ini = Tagihan Lainnya. */
    public static function packageCodes(): array
    {
        $codes = array_map(fn ($p) => $p['code'], self::packages());
        $codes[] = 'SPP';

        return array_values(array_unique($codes));
    }

    /** Urutan tampil sesuai tabel rancangan (awal → tengah → akhir semester). */
    public static function sequence(): array
    {
        return [
            'SRG-GJL' => 10, 'DU-GJL' => 10,
            'SPP-1' => 20, 'SPP-2' => 40, 'SPP-3' => 70,
            'SPP-4' => 90, 'SPP-5' => 110, 'SPP-6' => 120,
            'LKS-GJL' => 30, 'LKS-GNP' => 30,
            'KOM-GJL' => 50, 'KOM-GNP' => 50,
            'LAB-IPA-GJL' => 60, 'LAB-IPA-GNP' => 60,
            'STD-IPS-GJL' => 60, 'STD-IPS-GNP' => 60,
            'LAB-IBB-GJL' => 60, 'LAB-IBB-GNP' => 60,
            'PEN-IBB-GJL' => 80, 'PEN-IBB-GNP' => 80,
            'UTS-GJL' => 100, 'UTS-GNP' => 100,
            'PSAJ-GNP' => 105,
            'UAS-GJL' => 130, 'UAS-GNP' => 130,
            'SPP' => 25,
        ];
    }

    /** Bulan ke-1..6 dalam semester dari bulan kalender (Jul–Des / Jan–Jun). */
    public static function sppBulan(?int $periodMonth): ?int
    {
        if ($periodMonth === null) {
            return null;
        }
        if ($periodMonth >= 7 && $periodMonth <= 12) {
            return $periodMonth - 6;
        }
        if ($periodMonth >= 1 && $periodMonth <= 6) {
            return $periodMonth;
        }

        return null;
    }

    /** Nomor urut tampil tagihan dari kode kategori + bulan periode. */
    public static function sequenceIndexFor(string $categoryCode, ?int $periodMonth = null): int
    {
        $code = strtoupper(trim($categoryCode));
        if ($code === 'SPP') {
            $b = self::sppBulan($periodMonth);
            if ($b !== null) {
                return (int) self::sequence()['SPP-'.$b];
            }

            return 25;
        }

        return self::sequence()[$code] ?? 500;
    }

    /** Nomor urut kategori (tanpa bulan) untuk pilihan di form. */
    public static function categorySequence(string $code): int
    {
        return self::sequence()[strtoupper(trim($code))] ?? 500;
    }

    /** Nama ramah tampil: SPP jadi "SPP Bulan N", lainnya ikut nama kategori. */
    public static function displayName(string $categoryCode, ?string $categoryName, ?int $periodMonth = null): string
    {
        if (strtoupper(trim($categoryCode)) === 'SPP') {
            $b = self::sppBulan($periodMonth);
            if ($b !== null) {
                return 'SPP Bulan '.$b;
            }
        }

        return $categoryName ?: $categoryCode;
    }
}
