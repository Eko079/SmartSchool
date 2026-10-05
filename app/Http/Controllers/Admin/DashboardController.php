<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Support\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // HP => tampilan mobile otomatis (URL tetap /admin, tanpa /m).
        if (Device::isPhone($request)) {
            return app(MobileController::class)->dashboard();
        }

        Carbon::setLocale('id');
        $now = Carbon::now();

        // ---------- Kartu statistik ----------
        $totalIncome = (float) Payment::where('status', 'success')->sum('amount');
        $incomeThisMonth = (float) Payment::where('status', 'success')
            ->whereYear('paid_at', $now->year)
            ->whereMonth('paid_at', $now->month)
            ->sum('amount');
        $lastMonth = $now->copy()->subMonthNoOverflow();
        $incomeLastMonth = (float) Payment::where('status', 'success')
            ->whereYear('paid_at', $lastMonth->year)
            ->whereMonth('paid_at', $lastMonth->month)
            ->sum('amount');

        // Delta pemasukan vs bulan lalu (null bila bulan lalu belum ada data).
        $incomeDelta = null;
        $incomeDeltaUp = true;
        if ($incomeLastMonth > 0) {
            $pct = ($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth * 100;
            $incomeDeltaUp = $pct >= 0;
            $incomeDelta = ($pct >= 0 ? '+' : '-') . number_format(abs($pct), 1, ',', '.') . '%';
        }

        $totalBilled = (float) Bill::sum('amount');
        $totalPaid = (float) Bill::sum('paid_amount');
        $totalPending = max(0, $totalBilled - $totalPaid);
        $unpaidCount = Bill::where('status', 'unpaid')->count();
        $overdueCount = Bill::where('status', 'overdue')->count();

        $activeStudents = Student::where('status', 'aktif')->count();
        $totalStudents = Student::count();
        $collectionRate = $totalBilled > 0 ? round(($totalPaid / $totalBilled) * 100, 1) : 0;

        $recentPayments = Payment::with(['student.classRoom', 'bill.feeCategory'])
            ->where('status', 'success')
            ->latest('paid_at')
            ->take(6)
            ->get();

        // ---------- Bar chart: pemasukan 12 bulan terakhir ----------
        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $fullMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $rows = Payment::where('status', 'success')
            ->where('paid_at', '>=', $now->copy()->startOfMonth()->subMonthsNoOverflow(11))
            ->selectRaw('YEAR(paid_at) AS y, MONTH(paid_at) AS m, SUM(amount) AS total')
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn ($r) => $r->y . '-' . $r->m);

        $monthlyIncome = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = $now->copy()->subMonthsNoOverflow($i);
            $total = (float) ($rows[$d->year . '-' . $d->month]->total ?? 0);
            $monthlyIncome[] = [
                'label' => $shortMonths[$d->month - 1],
                'title' => $fullMonths[$d->month - 1] . ' ' . $d->year,
                'total' => $total,
                'current' => $i === 0,
            ];
        }

        // ---------- Donat: distribusi penerimaan per kategori ----------
        $categories = FeeCategory::withCount('bills')
            ->withSum('bills as total_collected', 'paid_amount')
            ->get();
        $collectedTotal = (float) $categories->sum('total_collected');

        $palette = [
            ['stroke' => '#2563eb', 'dot' => 'bg-blue-600'],
            ['stroke' => '#22c55e', 'dot' => 'bg-emerald-500'],
            ['stroke' => '#f59e0b', 'dot' => 'bg-amber-500'],
        ];
        $sorted = $categories->sortByDesc('total_collected')->values();
        $categoryShares = [];
        foreach ($sorted->take(3) as $i => $cat) {
            $amount = (float) $cat->total_collected;
            $categoryShares[] = [
                'code' => $cat->code,
                'name' => $cat->name,
                'amount' => $amount,
                'percent' => $collectedTotal > 0 ? round($amount / $collectedTotal * 100, 1) : 0,
                'stroke' => $palette[$i]['stroke'],
                'dot' => $palette[$i]['dot'],
            ];
        }
        $restTotal = (float) $sorted->slice(3)->sum('total_collected');
        if ($restTotal > 0 && $collectedTotal > 0) {
            $categoryShares[] = [
                'code' => 'Lainnya',
                'name' => 'Kategori lainnya',
                'amount' => $restTotal,
                'percent' => round($restTotal / $collectedTotal * 100, 1),
                'stroke' => '#94a3b8',
                'dot' => 'bg-slate-400',
            ];
        }
        $categoryTop = $categoryShares[0] ?? null;

        // ---------- Aktivitas terbaru (dari data nyata) ----------
        $activities = collect();
        foreach (Payment::with('student')->where('status', 'success')->latest('paid_at')->take(2)->get() as $pay) {
            $ts = $pay->paid_at ?? $pay->created_at;
            $activities->push([
                'message' => 'Pembayaran ' . $pay->invoice_number . ' lunas — ' . ($pay->student->name ?? 'siswa'),
                'time' => $ts?->diffForHumans() ?? '',
                'dot' => 'bg-emerald-500',
                'ts' => $ts?->timestamp ?? 0,
            ]);
        }
        foreach (Bill::with('student')->whereIn('status', ['unpaid', 'overdue'])->latest()->take(2)->get() as $bill) {
            $isOverdue = $bill->status === 'overdue';
            $activities->push([
                'message' => 'Tagihan ' . $bill->bill_code
                    . ($isOverdue ? ' menunggak' : ' menunggu pembayaran')
                    . ' — ' . ($bill->student->name ?? 'siswa'),
                'time' => $bill->created_at?->diffForHumans() ?? '',
                'dot' => $isOverdue ? 'bg-rose-500' : 'bg-amber-500',
                'ts' => $bill->created_at?->timestamp ?? 0,
            ]);
        }
        foreach (Student::with('classRoom')->latest()->take(1)->get() as $st) {
            $activities->push([
                'message' => 'Siswa baru ' . $st->name . ' terdaftar'
                    . ($st->classRoom ? ' (' . $st->classRoom->name . ')' : ''),
                'time' => $st->created_at?->diffForHumans() ?? '',
                'dot' => 'bg-blue-600',
                'ts' => $st->created_at?->timestamp ?? 0,
            ]);
        }
        $activities = $activities->sortByDesc('ts')->take(4)->values()
            ->map(fn ($a) => ['message' => $a['message'], 'time' => $a['time'], 'dot' => $a['dot']]);

        // ---------- Realisasi bulan berjalan ----------
        $billedThisMonth = (float) Bill::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');
        $realizationPct = $billedThisMonth > 0 ? round($incomeThisMonth / $billedThisMonth * 100, 1) : 0;

        return view('admin.dashboard', compact(
            'totalIncome',
            'incomeDelta',
            'incomeDeltaUp',
            'totalPending',
            'unpaidCount',
            'overdueCount',
            'activeStudents',
            'totalStudents',
            'collectionRate',
            'totalBilled',
            'totalPaid',
            'recentPayments',
            'monthlyIncome',
            'categoryShares',
            'categoryTop',
            'activities',
            'billedThisMonth',
            'incomeThisMonth',
            'realizationPct'
        ));
    }

    /** Format rupiah ringkas: 42,8 Jt — 1,2 M — 850 rb. */
    public static function rpShort(float $value): string
    {
        $trim = fn ($v) => rtrim(rtrim(number_format($v, 1, ',', '.'), '0'), ',');
        if ($value >= 1_000_000_000) {
            return 'Rp ' . $trim($value / 1_000_000_000) . ' M';
        }
        if ($value >= 1_000_000) {
            return 'Rp ' . $trim($value / 1_000_000) . ' Jt';
        }
        if ($value >= 1_000) {
            return 'Rp ' . $trim($value / 1_000) . ' rb';
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
