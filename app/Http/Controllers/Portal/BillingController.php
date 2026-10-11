<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Concerns\RendersPortalMobile;
use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\ReceiptService;
use App\Services\WaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    use RendersPortalMobile;
    protected function student()
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();

        return $user->student;
    }

    protected function scopeOwned(Request $request)
    {
        $student = $this->student();
        abort_if(! $student, 422, 'Siswa tidak tertaut ke akun wali.');

        return Bill::with(['feeCategory', 'student.classRoom', 'payments'])->where('student_id', $student->id);
    }

    public function index(Request $request)
    {
        // Dropdown gabungan "Pilih Semester" dipecah ke academic_year + semester.
        if ($request->filled('semester_key') && str_contains((string) $request->semester_key, '|')) {
            [$ky, $ks] = explode('|', (string) $request->semester_key, 2);
            $request->merge(['academic_year' => $ky, 'semester' => $ks]);
        }
        $query = $this->scopeOwned($request);

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($w) use ($q) {
                $w->where('bill_code', 'like', "%{$q}%")
                    ->orWhere('order_id', 'like', "%{$q}%")
                    ->orWhereHas('feeCategory', fn ($f) => $f->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('overdue_only') && $request->boolean('overdue_only')) {
            $query->where('status', 'overdue');
        }

        if ($request->filled('academic_year')) {
            $taFilter = $request->string('academic_year')->toString();
            $query->where('academic_year', $taFilter);
        }

        if ($request->filled('fee_category_id')) {
            $query->where('fee_category_id', (int) $request->integer('fee_category_id'));
        }

        $perPage = (int) $request->integer('per_page', 25);
        $perPage = $perPage > 0 && $perPage <= 50 ? $perPage : 25;

        // Halaman ini selalu per semester terpilih: default ikut Pengaturan admin.
        $activeSemester = strtolower((string) \App\Models\SchoolSetting::get('active_semester', 'Ganjil')) === 'genap' ? 'genap' : 'ganjil';
        $selSemester = $request->filled('semester') ? $request->semester : $activeSemester;
        if (! in_array($selSemester, ['ganjil', 'genap'], true)) {
            $selSemester = $activeSemester;
        }
        $query->where('semester', $selSemester);

        // Daftar utama = paket semester (tabel rancangan). Non-paket ikut semester
        // yang sama tapi tampil di bagian "Tagihan Lainnya".
        $packageIds = \App\Models\FeeCategory::whereIn('code', \App\Support\PackageRules::packageCodes())->pluck('id');
        $query->whereIn('fee_category_id', $packageIds);

        // Default TA ikut Pengaturan, fallback TA terbaru siswa.
        $selYear = $request->filled('academic_year') ? $request->string('academic_year')->toString() : null;
        if (! $selYear) {
            $selYear = \App\Models\SchoolSetting::get('academic_year')
                ?: Bill::where('student_id', $this->student()->id)->whereNotNull('academic_year')->orderByDesc('academic_year')->value('academic_year');
            if ($selYear) {
                $query->where('academic_year', $selYear);
            }
        }

        // Urutan prioritas bayar: menunggak, belum bayar, sebagian, lunas; lalu jatuh tempo terdekat.
        $bills = $query->orderByRaw("FIELD(status, 'overdue', 'unpaid', 'partial', 'paid')")->orderBy('sort_order')->orderBy('due_date')->paginate($perPage)->withQueryString();
        $waitingQuery = (clone $query)->whereIn('status', ['unpaid', 'partial', 'overdue']);
        $summary = [
            'waiting' => (float) $waitingQuery->sum('amount') - (float) (clone $waitingQuery)->sum('paid_amount') + (float) (clone $waitingQuery)->sum('fine_amount'),
            'waiting_principal' => (float) $waitingQuery->sum('amount') - (float) (clone $waitingQuery)->sum('paid_amount'),
            'waiting_fine' => (float) (clone $waitingQuery)->sum('fine_amount'),
            'paid' => (float) (clone $query)->where('status', 'paid')->sum('amount'),
        ];

        $studentId = $this->student()->id;
        // Siswa lulus/nonaktif: portal jadi mode riwayat (baca saja, tombol bayar disembunyikan).
        $isArchived = (($this->student()->status ?? 'aktif') !== 'aktif');
        $semesters = Bill::where('student_id', $studentId)->whereNotNull('semester')->distinct()->pluck('semester')->values();
        $academicYears = Bill::where('student_id', $studentId)->whereNotNull('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year')->values();
        $categories = \App\Models\FeeCategory::whereIn('id', Bill::where('student_id', $studentId)->distinct()->pluck('fee_category_id'))->orderBy('name')->get(['id', 'name']);

        if ($request->expectsJson() || ! view()->exists('portal.tagihan')) {
            return response()->json(['bills' => $bills, 'summary' => $summary]);
        }

        // Opsi semester gabungan "GANJIL 2026/2027" untuk dropdown Pilih Semester.
        $semesterOptions = Bill::where('student_id', $studentId)->whereNotNull('academic_year')->whereNotNull('semester')->select('academic_year', 'semester')->distinct()->get()
            ->map(fn ($p) => ['key' => $p->academic_year.'|'.$p->semester, 'label' => strtoupper($p->semester).' '.$p->academic_year, 'year' => $p->academic_year, 'sem' => $p->semester])
            ->sort(function ($a, $b) {
                if ($a['year'] !== $b['year']) {
                    return $b['year'] <=> $a['year'];
                }

                return $a['sem'] <=> $b['sem'];
            })->values();
        $selKey = ($selYear ?? '').'|'.($selSemester ?? '');
        if ($selYear && $selSemester && ! $semesterOptions->contains('key', $selKey)) {
            $semesterOptions->prepend(['key' => $selKey, 'label' => strtoupper($selSemester).' '.$selYear, 'year' => $selYear, 'sem' => $selSemester]);
        }

        // Tagihan di luar semester terpilih (bagian "Tagihan Lainnya").
        $otherBills = collect();
        $otherTotal = 0;
        if ($selYear && $selSemester) {
            $otherQuery = $this->scopeOwned($request);
            if ($request->filled('q')) {
                $q2 = $request->string('q')->toString();
                $otherQuery->where(function ($w) use ($q2) {
                    $w->where('bill_code', 'like', "%{$q2}%")
                        ->orWhere('order_id', 'like', "%{$q2}%")
                        ->orWhereHas('feeCategory', fn ($f) => $f->where('name', 'like', "%{$q2}%"));
                });
            }
            if ($request->filled('status') && $request->status !== 'all') {
                $otherQuery->where('status', $request->status);
            }
            if ($request->filled('overdue_only') && $request->boolean('overdue_only')) {
                $otherQuery->where('status', 'overdue');
            }
            if ($request->filled('fee_category_id')) {
                $otherQuery->where('fee_category_id', (int) $request->integer('fee_category_id'));
            }
            $otherQuery->where('academic_year', $selYear)->where('semester', $selSemester);
            $otherQuery->whereNotIn('fee_category_id', \App\Models\FeeCategory::whereIn('code', \App\Support\PackageRules::packageCodes())->pluck('id'));
            $otherTotal = (clone $otherQuery)->count();
            $otherBills = $otherQuery->orderByRaw("FIELD(status, 'overdue', 'unpaid', 'partial', 'paid')")->orderBy('sort_order')->orderBy('due_date')->take(10)->get();
        }

        return $this->portalView($request, 'portal.tagihan', 'portal-mobile.tagihan', compact('bills', 'summary', 'semesters', 'academicYears', 'categories', 'selSemester', 'selYear', 'semesterOptions', 'selKey', 'otherBills', 'otherTotal', 'isArchived'));
    }

    public function show(Request $request, Bill $bill)
    {
        $student = $this->student();
        abort_if(! $student || $bill->student_id !== $student->id, 403);
        $isArchived = (($student->status ?? 'aktif') !== 'aktif');

        $bill->load(['feeCategory', 'student.classRoom', 'payments', 'events']);

        if ($request->expectsJson() || ! view()->exists('portal.tagihan-detail')) {
            return response()->json(['bill' => $bill]);
        }

        return $this->portalView($request, 'portal.tagihan-detail', 'portal-mobile.tagihan-detail', compact('bill', 'isArchived'));
    }

    public function pay(Request $request, Bill $bill, MidtransService $midtrans, WaService $wa)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:bca_va,bni_va,qris'],
        ]);

        $student = $this->student();
        abort_if(! $student || $bill->student_id !== $student->id, 403);
        abort_if(($student->status ?? 'aktif') !== 'aktif', 422, 'Akun arsip: pembayaran dinonaktifkan, hubungi operator.');
        abort_if($bill->status === 'paid', 422, 'Tagihan sudah lunas.');

        $trx = $midtrans->createTransaction($bill, $validated['method']);
        $bill->forceFill([
            'order_id' => $trx['order_id'],
            'va_number' => $trx['va_number'],
            'qris_payload' => $validated['method'] === 'qris' ? $trx['va_number'] : null,
            'expired_at' => $trx['expired_at'],
        ])->save();

        BillEvent::create([
            'bill_id' => $bill->id,
            'event' => 'pay_initiated',
            'message' => 'Pembayaran dimulai via ' . $validated['method'] . ' order ' . $trx['order_id'],
        ]);

        $user = Auth::guard('wali')->user() ?? Auth::user();
        if ($user && $user->notify_wa && $user->phone) {
            $wa->send($user->id, $bill->id, $user->phone, 'pay_initiated');
        }

        return response()->json(['bill' => $bill->fresh(), 'transaction' => $trx], 201);
    }

    public function history(Request $request)
    {
        $student = $this->student();
        abort_if(! $student, 422);

        $payments = Payment::with(['bill.feeCategory'])
            ->where('student_id', $student->id)
            ->latest('paid_at')
            ->paginate(10);

        if ($request->expectsJson() || ! view()->exists('portal.riwayat')) {
            return response()->json(['payments' => $payments]);
        }

        return $this->portalView($request, 'portal.riwayat', 'portal-mobile.riwayat', compact('payments'));
    }

    public function receipt(Request $request, Payment $payment, ReceiptService $receipts)
    {
        $student = $this->student();
        abort_if(! $student || $payment->student_id !== $student->id, 403);

        if ($request->get('format') === 'txt') {
            return response($receipts->receiptText($payment), 200, ['Content-Type' => 'text/plain']);
        }

        return response($receipts->receiptHtml($payment), 200, ['Content-Type' => 'text/html']);
    }

    public function callback(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();
        abort_unless($midtrans->verifyCallback($payload), 422, 'Callback tidak valid.');

        $bill = Bill::where('order_id', $payload['order_id'])->firstOrFail();
        $status = $payload['transaction_status'] ?? '';

        if (in_array($status, ['settlement', 'capture'])) {
            // Segarkan denda terkini lalu lunasi total pokok + denda.
            $calc = Bill::calcFine($bill->due_date);
            $bill->forceFill(['fine_amount' => $calc['fine']])->save();
            $bill->refresh();
            $totalDue = $bill->total_due;
            $amount = (float) ($payload['gross_amount'] ?? $totalDue);
            // Jangan catat kurang dari total wajib (pokok + denda).
            $amount = max($amount, $totalDue);
            $bill->loadMissing('feeCategory');
            $existing = Payment::where('bill_id', $bill->id)->where('status', 'success')->first();
            $attrs = [
                'bill_id' => $bill->id,
                'student_id' => $bill->student_id,
                'amount' => $amount,
                'payment_method' => $payload['payment_type'] ?? 'midtrans',
                'gateway' => 'midtrans',
                'transaction_id' => $payload['transaction_id'] ?? null,
                'va_number' => $bill->va_number,
                'settlement_at' => now(),
                'callback_payload' => $payload,
                'status' => 'success',
                'paid_at' => now(),
            ];
            if ($existing) {
                $existing->fill($attrs)->save();
                $payment = $existing->fresh();
            } else {
                $attrs['invoice_number'] = Payment::nextInvoiceNumber($bill->feeCategory->code ?? null);
                $payment = Payment::create($attrs);
            }
            $bill->forceFill([
                'paid_amount' => $bill->amount,
                'fine_amount' => $calc['fine'],
                'status' => 'paid',
            ])->save();

            BillEvent::create(['bill_id' => $bill->id, 'event' => 'settlement', 'message' => 'Settlement otomatis order ' . $payload['order_id'] . ' total Rp ' . number_format($amount, 0, ',', '.') . ' (pokok + denda)']);

            return response()->json(['message' => 'OK', 'payment_id' => $payment->id]);
        }

        if (in_array($status, ['expire', 'cancel', 'deny'])) {
            BillEvent::create(['bill_id' => $bill->id, 'event' => 'payment_' . $status, 'message' => 'Midtrans callback: ' . $status]);

            return response()->json(['message' => 'Callback dicatat.']);
        }

        return response()->json(['message' => 'Status diabaikan.']);
    }
}
