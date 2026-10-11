<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\FeeCategory;
use App\Models\Student;
use Illuminate\Console\Command;

class GenerateSemesterPackage extends Command
{
    protected $signature = 'bills:generate-package {academic_year} {semester : ganjil|genap} {--classes=*}';

    protected $description = 'Generate paket tagihan semesteran (idempotent, lewati yang sudah ada).';

    public function handle(): int
    {
        $year = (string) $this->argument('academic_year');
        $semester = strtolower((string) $this->argument('semester'));
        if (! in_array($semester, ['ganjil', 'genap'], true)) {
            $this->error('Semester harus ganjil|genap.');

            return self::FAILURE;
        }

        $tag = $semester === 'ganjil' ? 'GJL' : 'GNP';
        $cats = FeeCategory::where('is_active', true)
            ->whereIn('type', ['semesteran', 'sekali'])
            ->where(function ($q) use ($semester) {
                $q->where('semester', $semester)->orWhereNull('semester');
            })
            ->orderBy('sort_order')->orderBy('id')
            ->get();
        if ($cats->isEmpty()) {
            $this->error("Tidak ada kategori semesteran aktif untuk {$semester}.");

            return self::FAILURE;
        }

        $classIds = (array) ($this->option('classes') ?? []);
        $students = Student::with('classRoom')->where('status', 'aktif')
            ->when($classIds, fn ($q) => $q->whereIn('class_id', $classIds))
            ->get();
        if ($students->isEmpty()) {
            $this->error('Tidak ada siswa aktif.');

            return self::FAILURE;
        }

        $calYear = $semester === 'ganjil'
            ? (int) (explode('/', $year)[0] ?? date('Y'))
            : (int) (explode('/', $year)[1] ?? date('Y'));

        $created = 0;
        $skipped = 0;
        foreach ($students as $student) {
            $major = $student->classRoom->major ?? null;
            $grade = $student->classRoom->grade ?? null;
            foreach ($cats as $cat) {
                if (! \App\Support\PackageRules::appliesToCategory($cat, $major, $grade, $student->entry_year ? (int) $student->entry_year : null, $year)) {
                    $skipped++;
                    continue;
                }
                $exists = Bill::where('student_id', $student->id)
                    ->where('fee_category_id', $cat->id)
                    ->where('academic_year', $year)
                    ->where('semester', $semester)
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
                $billCode = 'INV-'.$calYear.'-'.$tag.'-'.substr(strtoupper(preg_replace('/[^A-Z0-9]/', '', $cat->code ?? 'CAT')), 0, 3).'-'.str_pad($student->id, 4, '0', STR_PAD_LEFT);
                Bill::create([
                    'bill_code' => $billCode,
                    'student_id' => $student->id,
                    'fee_category_id' => $cat->id,
                    'period_month' => $semester === 'ganjil' ? 7 : 1,
                    'period_year' => $calYear,
                    'academic_year' => $year,
                    'semester' => $semester,
                    'amount' => $cat->default_amount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                    'due_date' => \App\Support\PackageRules::dueDateFor($cat, $year, $semester),
                    'sort_order' => \App\Support\PackageRules::sequenceIndexFor($cat->code, $semester === 'ganjil' ? 7 : 1),
                ]);
                $created++;
            }
        }

        $this->info("Paket {$semester} {$year}: {$created} dibuat, {$skipped} dilewati.");

        return self::SUCCESS;
    }
}
