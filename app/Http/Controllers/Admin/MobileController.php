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
            ->selectRaw('fee_category_id, period_month, period_year, COUNT(*) as total, SUM(amount) as nominal, MAX(created_at) as dibuat')
            ->groupBy('fee_category_id', 'period_month', 'period_year')
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

    /** Simpan pengaturan dari popup mobile — kembali ke /m, bukan /admin. */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:20',
            'active_semester' => 'nullable|in:Ganjil,Genap',
            'school_phone' => 'nullable|string|max:30',
            'school_email' => 'nullable|email|max:255',
        ]);

        foreach ($validated as $key => $value) {
            SchoolSetting::set($key, $value ?? '');
        }

        return redirect()->route('m.pengaturan')->with('success', 'Pengaturan berhasil disimpan.');
    }

    /** Bantuan mobile: FAQ + kontak dari pengaturan. */
    public function bantuan()
    {
        $settings = SchoolSetting::pluck('value', 'key')->toArray();
        $schoolName = $settings['school_name'] ?? 'SmartSchool';

        return view('mobile.bantuan', compact('settings', 'schoolName'));
    }

    /** JSON riwayat tagihan siswa (dipakai popup mobile, tetap di /m). */
    public function studentBills(Student $student)
    {
        $rows = $student->bills()->with('feeCategory')->latest()->take(5)->get()
            ->map(fn ($b) => [
                'bill_code' => $b->bill_code,
                'category' => $b->feeCategory->name ?? '-',
                'amount' => number_format($b->amount, 0, ',', '.'),
                'status' => $b->status,
            ]);

        return response()->json($rows);
    }

    /** Update siswa dari popup mobile — kembali ke /m, bukan /admin. */
    public function updateStudent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:20|unique:students,nisn,' . $student->id,
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'status' => 'required|in:aktif,cuti,lulus',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'nis.unique' => 'NIS sudah terdaftar, gunakan NIS lain.',
            'nisn.unique' => 'NISN sudah terdaftar, gunakan NISN lain.',
        ]);

        $student->update($validated);

        return redirect()->route('m.siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /** Hapus siswa dari mobile — tolak bila punya riwayat, kembali ke /m. */
    public function destroyStudent(Student $student)
    {
        if ($student->bills()->exists() || $student->payments()->exists()) {
            return redirect()->route('m.siswa')
                ->with('error', "Siswa {$student->name} tidak dapat dihapus karena masih memiliki riwayat tagihan/pembayaran.");
        }

        $student->delete();

        return redirect()->route('m.siswa')->with('success', 'Data siswa berhasil dihapus.');
    }
}
