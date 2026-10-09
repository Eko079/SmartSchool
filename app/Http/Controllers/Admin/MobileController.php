<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\ClassRoom;
use App\Models\FeeCategory;
use App\Models\Payment;
use App\Models\SchoolSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    /** Data beranda mobile: hero + KPI + transaksi terbaru + target. */
    public function dashboard()
    {
        Carbon::setLocale('id');
        $now = Carbon::now();

        $incomeThisMonth = (float) Payment::where('status', 'success')
            ->whereYear('paid_at', $now->year)
            ->whereMonth('paid_at', $now->month)
            ->sum('amount');
        $lastMonth = $now->copy()->subMonthNoOverflow();
        $incomeLastMonth = (float) Payment::where('status', 'success')
            ->whereYear('paid_at', $lastMonth->year)
            ->whereMonth('paid_at', $lastMonth->month)
            ->sum('amount');

        $delta = null;
        $deltaUp = true;
        if ($incomeLastMonth > 0) {
            $pct = ($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth * 100;
            $deltaUp = $pct >= 0;
            $delta = ($pct >= 0 ? '+' : '-') . number_format(abs($pct), 1, ',', '.') . '%';
        }

        $totalBilled = (float) Bill::sum('amount');
        $totalPaid = (float) Bill::sum('paid_amount');
        $paidCount = Bill::where('status', 'paid')->count();
        $unpaidCount = Bill::whereIn('status', ['unpaid', 'overdue'])->count();
        $activeStudents = Student::where('status', 'aktif')->count();

        $billedThisMonth = (float) Bill::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');

        $recent = Payment::with(['student.classRoom', 'bill.feeCategory'])
            ->where('status', 'success')
            ->latest('paid_at')
            ->take(5)
            ->get();

        $schoolName = SchoolSetting::get('school_name', 'SmartSchool');

        return view('mobile.dashboard', compact(
            'incomeThisMonth', 'delta', 'deltaUp', 'totalBilled', 'totalPaid',
            'paidCount', 'unpaidCount', 'activeStudents', 'billedThisMonth',
            'recent', 'schoolName', 'now'
        ));
    }

    /** Data siswa mobile: KPI + kartu siswa + filter kelas. */
    public function siswa(Request $request)
    {
        $query = Student::with('classRoom');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $query->latest();
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }
        $students = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Student::count(),
            'aktif' => Student::where('status', 'aktif')->count(),
            'baru' => Student::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'nonaktif' => Student::where('status', '!=', 'aktif')->count(),
        ];
        $classes = ClassRoom::orderBy('name')->get();
        $academicYear = SchoolSetting::get('academic_year', date('Y') . '/' . (date('Y') + 1));
        $schoolName = SchoolSetting::get('school_name', 'SmartSchool');

        return view('mobile.siswa', compact('students', 'stats', 'classes', 'academicYear', 'schoolName'));
    }

    /** Kategori mobile: kartu kategori + statistik nyata. */
    public function kategori(Request $request)
    {
        $query = FeeCategory::withCount('bills')
            ->withSum('bills as total_collected', 'paid_amount');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        $categories = $query->orderBy('id')->get();
        $stats = [
            'total' => FeeCategory::count(),
            'aktif' => FeeCategory::where('is_active', true)->count(),
            'bills' => \App\Models\Bill::count(),
            'revenue' => (float) \App\Models\Bill::sum('paid_amount'),
        ];
        $schoolName = SchoolSetting::get('school_name', 'SmartSchool');

        return view('mobile.kategori', compact('categories', 'stats', 'schoolName'));
    }

    /** Tagihan mobile: kartu kategori + tagihan terbaru + riwayat batch. */
    public function tagihan(Request $request)
    {
        $categories = FeeCategory::withCount('bills')
            ->withSum('bills as total_collected', 'paid_amount')
            ->orderBy('id')
            ->get();

        $bills = Bill::with(['student.classRoom', 'feeCategory'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = $request->input('q');
                $q->where('bill_code', 'like', "%{$s}%")
                    ->orWhereHas('student', fn ($st) => $st->where('name', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $history = Bill::with('feeCategory')
            ->selectRaw('fee_category_id, period_month, period_year, academic_year, semester, COUNT(*) as total, SUM(amount) as nominal, MAX(created_at) as dibuat')
            ->groupBy('fee_category_id', 'period_month', 'period_year', 'academic_year', 'semester')
            ->orderByDesc('dibuat')
            ->take(5)
            ->get();

        $stats = [
            'total' => Bill::count(),
            'lunas' => Bill::where('status', 'paid')->count(),
            'tunggakan' => Bill::whereIn('status', ['unpaid', 'overdue'])->count(),
            'nominal' => (float) Bill::sum('amount'),
        ];
        $schoolName = SchoolSetting::get('school_name', 'SmartSchool');

        return view('mobile.tagihan', compact('categories', 'bills', 'history', 'stats', 'schoolName'));
    }

    /** Laporan mobile: KPI + tren 6 bulan + pembayaran terbaru. */
    public function laporan(Request $request)
    {
        $totalIncome = (float) Payment::where('status', 'success')->sum('amount');
        $successCount = Payment::where('status', 'success')->count();
        $totalBilled = (float) Bill::sum('amount');
        $totalPaid = (float) Bill::sum('paid_amount');
        $rate = $totalBilled > 0 ? round($totalPaid / $totalBilled * 100, 1) : 0;

        $labels = [];
        $trend = [];
        $now = now();
        for ($i = 5; $i >= 0; $i--) {
            $d = $now->copy()->subMonths($i);
            $labels[] = $d->locale('id')->shortMonthName;
            $trend[] = (float) Payment::where('status', 'success')
                ->whereYear('paid_at', $d->year)
                ->whereMonth('paid_at', $d->month)
                ->sum('amount');
        }
        $maxTrend = max(1, max($trend));

        $payments = Payment::with(['student.classRoom', 'bill.feeCategory'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = $request->input('q');
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhereHas('student', fn ($st) => $st->where('name', 'like', "%{$s}%"));
            })
            ->latest('paid_at')
            ->paginate(10)
            ->withQueryString();

        $schoolName = SchoolSetting::get('school_name', 'SmartSchool');

        return view('mobile.laporan', compact('totalIncome', 'successCount', 'rate', 'labels', 'trend', 'maxTrend', 'payments', 'schoolName'));
    }

    /** Pengaturan mobile: profil + tahun ajaran + gateway. */
    public function pengaturan()
    {
        $settings = SchoolSetting::pluck('value', 'key')->toArray();
        $schoolName = $settings['school_name'] ?? 'SmartSchool';

        return view('mobile.pengaturan', compact('settings', 'schoolName'));
    }

    /** Bantuan mobile: FAQ + kontak dari pengaturan. */
    public function bantuan()
    {
        $settings = SchoolSetting::pluck('value', 'key')->toArray();
        $schoolName = $settings['school_name'] ?? 'SmartSchool';

        return view('mobile.bantuan', compact('settings', 'schoolName'));
    }

}
