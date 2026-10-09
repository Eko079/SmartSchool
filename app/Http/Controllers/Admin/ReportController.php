<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\SchoolSetting;
use App\Support\Device;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // HP => tampilan mobile otomatis (URL tetap /admin/laporan).
        if (Device::isPhone($request)) {
            return app(MobileController::class)->laporan($request);
        }

        $query = Payment::with(['student.classRoom', 'bill.feeCategory']);

        // ---------- Ambil daftar opsi filter dari DB (bukan hardcode) ----------
        $methods = Payment::distinct()->orderBy('payment_method')->pluck('payment_method');
        $statuses = Payment::distinct()->orderBy('status')->pluck('status');

        $method = $request->input('method');
        if ($method && $methods->contains($method)) {
            $query->where('payment_method', $method);
        }

        $status = $request->input('status');
        if ($status && $statuses->contains($status)) {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('invoice_number', 'like', "%{$q}%")
                    ->orWhereHas('student', function ($s) use ($q) {
                        $s->where('name', 'like', "%{$q}%")->orWhere('nis', 'like', "%{$q}%");
                    });
            });
        }

        // ---------- Sorting via klik header ----------
        $allowedSorts = ['invoice', 'student', 'amount', 'date', 'status'];
        $sort = $request->input('sort');
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = null;
        }
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'invoice') {
            $query->orderBy('invoice_number', $dir);
        } elseif ($sort === 'amount') {
            $query->orderBy('amount', $dir);
        } elseif ($sort === 'date') {
            $query->orderBy('paid_at', $dir);
        } elseif ($sort === 'status') {
            $query->orderBy('status', $dir);
        } elseif ($sort === 'student') {
            $query->whereHas('student')->join('students', 'students.id', '=', 'payments.student_id')
                ->orderBy('students.name', $dir)->select('payments.*');
        } else {
            $query->latest('paid_at');
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $total = (clone $query)->count();
        $payments = $total > $perPage
            ? $query->paginate($perPage)->withQueryString()
            : $query->get();

        // ---------- Chart tren 12 bulan nyata (pembayaran sukses) ----------
        $trend = array_fill(0, 12, 0);
        $labels = [];
        $now = now();
        for ($i = 11; $i >= 0; $i--) {
            $d = $now->copy()->subMonths($i);
            $labels[] = $d->locale('id')->shortMonthName;
            $trend[11 - $i] = (float) Payment::where('status', 'success')
                ->whereYear('paid_at', $d->year)->whereMonth('paid_at', $d->month)->sum('amount');
        }

        // ---------- Donat komposisi status tagihan nyata ----------
        $billGroups = Bill::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $donut = [
            'paid' => (int) ($billGroups['paid'] ?? 0),
            'waiting' => (int) (($billGroups['unpaid'] ?? 0) + ($billGroups['partial'] ?? 0)),
            'overdue' => (int) ($billGroups['overdue'] ?? 0),
        ];
        $donutTotal = max(array_sum($donut), 1);

        $totalIncome = Payment::where('status', 'success')->sum('amount');
        $totalBilled = Bill::sum('amount');
        $collectionRate = $totalBilled > 0 ? round(($totalIncome / $totalBilled) * 100, 1) : 0;
        $totalTransactions = Payment::count();
        $totalSuccess = Payment::where('status', 'success')->count();

        $stats = [
            'total_income' => $totalIncome,
            'collection_rate' => $collectionRate,
            'total_transactions' => $totalTransactions,
            'total_success' => $totalSuccess,
        ];

        if ($request->get('export') === 'csv') {
            $rows = (clone $query)->orderByDesc('paid_at')->get();
            $csv = "\xEF\xBB\xBFInvoice,Siswa,NIS,Kelas,Kategori,TA,Semester,Nominal,Tgl Bayar,Metode,Status\n";
            foreach ($rows as $r) {
                $csv .= sprintf("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                    '"' . str_replace('"', '""', $r->invoice_number) . '"',
                    '"' . str_replace('"', '""', $r->student->name ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->student->nis ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->student->classRoom->name ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->bill->feeCategory->name ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->bill->academic_year ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->bill->semester ?? '-') . '"',
                    $r->amount,
                    '"' . ($r->paid_at?->format('Y-m-d H:i') ?? '-') . '"',
                    '"' . str_replace('"', '""', $r->method_label) . '"',
                    '"' . $r->status . '"'
                );
            }

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="laporan-pembayaran.csv"',
            ]);
        }

        $academicYear = SchoolSetting::get('academic_year', date('Y') . '/' . (date('Y') + 1));

        return view('admin.laporan.index', compact('payments', 'stats', 'methods', 'statuses', 'trend', 'labels', 'donut', 'donutTotal', 'academicYear'));
    }
}

