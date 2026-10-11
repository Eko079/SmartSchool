<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\FeeCategory;
use App\Models\Student;
use App\Support\PackageRules;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SemesterPackageSeeder extends Seeder
{
    public function run(): void
    {
        // Satu sumber aturan: App\Support\PackageRules::packages().
        $packages = PackageRules::packages();

        $cats = [];
        foreach ($packages as $p) {
            $due = Carbon::parse($p['due']);
            $cats[$p['code']] = FeeCategory::updateOrCreate(['code' => $p['code']], [
                'name' => $p['name'], 'type' => $p['type'], 'default_amount' => $p['default_amount'],
                'description' => $p['description'], 'is_active' => true,
                'semester' => $p['semester'],
                'majors' => $p['majors'], 'grades' => $p['grades'],
                'audience' => ($p['skip_new'] ?? false) ? 'lama' : ((($p['new_only'] ?? false)) ? 'baru' : null),
                'due_month' => $due->month, 'due_day' => $due->day,
                'sort_order' => PackageRules::sequenceIndexFor($p['code'], $p['month']),
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
            $entry = $st->entry_year ? (int) $st->entry_year : null;
            foreach ($packages as $p) {
                // Daftar Ulang hanya Ganjil.
                if ($p['semester'] === 'genap' && str_starts_with($p['code'], 'DU-')) {
                    continue;
                }
                if (! PackageRules::appliesTo($p['code'], $major, $grade, $entry, '2026/2027')) {
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
                        'sort_order' => PackageRules::sequenceIndexFor($p['code'], $p['month']),
                    ]
                );
            }
        }
    }
}
