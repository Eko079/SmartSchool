<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\ClassRoom;
use App\Models\FeeCategory;
use App\Models\Student;
use App\Support\Device;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        // HP => tampilan mobile otomatis (URL tetap /admin/billing).
        if (Device::isPhone($request)) {
            return app(MobileController::class)->tagihan($request);
        }

        $categories = FeeCategory::where('is_active', true)->get();
        $classes = ClassRoom::withCount(['students' => function ($q) {
            $q->where('status', 'aktif');
        }])->get();

        $recentBills = Bill::with(['student.classRoom', 'feeCategory'])
            ->latest()
            ->take(8)
            ->get();

        // ---------- Riwayat batch nyata: kelompokkan tagihan per kategori+periode+semester ----------
        $history = Bill::with('feeCategory')
            ->selectRaw('fee_category_id, period_month, period_year, academic_year, semester, COUNT(*) as total, SUM(amount) as nominal, MAX(created_at) as dibuat')
            ->groupBy('fee_category_id', 'period_month', 'period_year', 'academic_year', 'semester')
            ->orderByDesc('dibuat')
            ->take(10)
            ->get();

        $stats = [
            'total_students' => Student::where('status', 'aktif')->count(),
            'total_bills' => Bill::count(),
            'total_unpaid' => Bill::whereIn('status', ['unpaid', 'overdue'])->count(),
        ];

        return view('admin.billing.index', compact('categories', 'classes', 'recentBills', 'history', 'stats'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'fee_category_id' => 'required|exists:fee_categories,id',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2020|max:2035',
            'academic_year' => 'nullable|string|max:9',
            'semester' => 'nullable|in:ganjil,genap',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:classes,id',
        ]);

        $students = Student::whereIn('class_id', $validated['classes'])
            ->where('status', 'aktif')
            ->get();

        $category = FeeCategory::findOrFail($validated['fee_category_id']);
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($students as $student) {
            // Cegah duplikat: 1 siswa + 1 kategori + 1 periode = 1 tagihan.
            $dup = Bill::where('student_id', $student->id)
                ->where('fee_category_id', $category->id)
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year']);
            if (! empty($validated['academic_year'])) {
                $dup->where('academic_year', $validated['academic_year']);
            }
            if (! empty($validated['semester'])) {
                $dup->where('semester', $validated['semester']);
            }
            $exists = $dup->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // bill_code unik walau generate kategori berbeda di periode sama:
            // INV-[TAHUN][BULAN]-[KODE_KATEGORI]-[ID_SISWA]
            $catSlug = strtoupper(preg_replace('/[^A-Z0-9]/', '', $category->code ?? 'CAT'));
            $billCode = 'INV-' . $validated['period_year']
                . str_pad($validated['period_month'], 2, '0', STR_PAD_LEFT)
                . '-' . substr($catSlug, 0, 8)
                . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);

            Bill::create([
                'bill_code' => $billCode,
                'student_id' => $student->id,
                'fee_category_id' => $category->id,
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'academic_year' => $validated['academic_year'] ?? null,
                'semester' => $validated['semester'] ?? null,
                'amount' => $validated['amount'],
                'paid_amount' => 0,
                'status' => 'unpaid',
                'due_date' => $validated['due_date'],
            ]);

            $createdCount++;
        }

        return redirect()->route('admin.billing')->with('success', "Berhasil men-generate {$createdCount} tagihan ({$skippedCount} dilewati karena sudah ada).");
    }

    public function generatePackage(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:9',
            'semester' => 'required|in:ganjil,genap',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:classes,id',
        ]);

        $tag = $validated['semester'] === 'ganjil' ? 'GJL' : 'GNP';
        $cats = FeeCategory::where('is_active', true)
            ->where('type', 'semesteran')
            ->where('code', 'like', '%-' . $tag)
            ->get();

        if ($cats->isEmpty()) {
            return redirect()->route('admin.billing')->with('error', 'Tidak ada kategori semesteran aktif untuk semester ' . $validated['semester'] . '.');
        }

        $students = Student::whereIn('class_id', $validated['classes'])->where('status', 'aktif')->get();
        $created = 0;
        $skipped = 0;

        foreach ($students as $student) {
            foreach ($cats as $cat) {
                $exists = Bill::where('student_id', $student->id)
                    ->where('fee_category_id', $cat->id)
                    ->where('academic_year', $validated['academic_year'])
                    ->where('semester', $validated['semester'])
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $year = $validated['semester'] === 'ganjil' ? (int) explode('/', $validated['academic_year'])[0] : (int) (explode('/', $validated['academic_year'])[1] ?? date('Y'));
                $billCode = 'INV-' . $year . '-' . $tag . '-' . substr(strtoupper(preg_replace('/[^A-Z0-9]/', '', $cat->code)), 0, 3) . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);

                Bill::create([
                    'bill_code' => $billCode,
                    'student_id' => $student->id,
                    'fee_category_id' => $cat->id,
                    'period_month' => $validated['semester'] === 'ganjil' ? 7 : 1,
                    'period_year' => $year,
                    'academic_year' => $validated['academic_year'],
                    'semester' => $validated['semester'],
                    'amount' => $cat->default_amount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(14),
                ]);
                $created++;
            }
        }

        return redirect()->route('admin.billing')->with('success', "Paket semester {$validated['semester']} {$validated['academic_year']}: {$created} dibuat, {$skipped} dilewati.");
    }
}

