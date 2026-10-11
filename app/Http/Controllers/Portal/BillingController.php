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
use Illuminate\Support\Facades\Log;

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

        // Urutan tabel rancangan: sort_order lalu jatuh tempo (status hanya label).
        $bills = $query->orderBy('sort_order')->orderBy('due_date')->orderBy('id')->paginate($perPage)->withQueryString();
        $waitingQuery = (clone $query)->whereIn('status', ['unpaid', 'partial', 'overdue']);
        $summary = [
            'waiting' => (float) $waitingQuery->sum('amount') - (float) (clone $waitingQuery)->sum('paid_amount') + (float) (clone $waitingQuery)->sum('fine_amount'),
            'waiting_principal' => (float) $waitingQuery->sum('amount') - (float) (clone $waitingQuery)->sum('paid_amount'),
            'waiting_fine' => (float) (clone $waitingQuery)->sum('fine_amount'),
            'paid' => (float) (clone $query)->where('status', 'paid')->sum('amount'),
            'open_count' => (clone $waitingQuery)->count(),
            'paid_count' => (clone $query)->where('status', 'paid')->count(),
            'overdue_count' => (clone $query)->where('status', 'overdue')->count(),
        ];

        $studentId = $this->student()->id;
        // Siswa lulus/nonaktif: portal jadi mode riwayat (baca saja, tombol bayar disembunyikan).
        $isArchived = (($this->student()->status ?? 'aktif') !== 'aktif');
        $semesters = Bill::where('student_id', $studentId)->whereNotNull('semester')->distinct()->pluck('semester')->values();
        $academicYears = Bill::where('student_id', $studentId)->whereNotNull('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year')->values();
        if ($academicYears->isEmpty() && $selYear) {
            $academicYears = collect([$selYear]);
        }
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
            $otherBills = $otherQuery->orderBy('sort_order')->orderBy('due_date')->orderBy('id')->take(10)->get();
        }

        // Channel aktif dari Pengaturan (modal bayar hanya tampilkan yang aktif).
        $gateways = [
            'bca_va' => (string) \App\Models\SchoolSetting::get('gateway_bca_va', '1') === '1',
            'bni_va' => (string) \App\Models\SchoolSetting::get('gateway_bni_va', '1') === '1',
            'qris' => (string) \App\Models\SchoolSetting::get('gateway_qris', '1') === '1',
        ];

        return $this->portalView($request, 'portal.tagihan', 'portal-mobile.tagihan', compact('bills', 'summary', 'semesters', 'academicYears', 'categories', 'selSemester', 'selYear', 'semesterOptions', 'selKey', 'otherBills', 'otherTotal', 'isArchived', 'gateways'));
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

        $gateways = [
            'bca_va' => (string) \App\Models\SchoolSetting::get('gateway_bca_va', '1') === '1',
            'bni_va' => (string) \App\Models\SchoolSetting::get('gateway_bni_va', '1') === '1',
            'qris' => (string) \App\Models\SchoolSetting::get('gateway_qris', '1') === '1',
        ];

        return $this->portalView($request, 'portal.tagihan-detail', 'portal-mobile.tagihan-detail', compact('bill', 'isArchived', 'gateways'));
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
        abort_unless($midtrans->isMethodEnabled($validated['method']), 422, 'Metode pembayaran ini sedang nonaktif.');

        try {
            $trx = $midtrans->createTransaction($bill, $validated['method']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Gagal membuat pembayaran: ' . $e->getMessage()], 422);
        }
        $isQris = $validated['method'] === 'qris';
        $bill->forceFill([
            'order_id' => $trx['order_id'],
            'va_number' => $isQris ? null : ($trx['va_number'] ?? null),
            'qris_payload' => $isQris ? ($trx['qr_string'] ?? null) : null,
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

        $query = Payment::with(['bill.feeCategory'])->where('student_id', $student->id);
        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($w) use ($q) {
                $w->where('invoice_number', 'like', "%{$q}%")
                    ->orWhere('payment_method', 'like', "%{$q}%")
                    ->orWhereHas('bill', fn ($b) => $b->where('bill_code', 'like', "%{$q}%")
                        ->orWhereHas('feeCategory', fn ($f) => $f->where('name', 'like', "%{$q}%")));
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $payments = $query->latest('paid_at')->paginate(10)->withQueryString();

        if ($request->expectsJson() || ! view()->exists('portal.riwayat')) {
            return response()->json(['payments' => $payments]);
        }

        return $this->portalView($request, 'portal.riwayat', 'portal-mobile.riwayat', compact('payments'));
    }

    /** Dokumen cetak rekap seluruh riwayat pembayaran (tanpa potongan halaman). */
    public function historyPrint(Request $request)
    {
        $student = $this->student();
        abort_if(! $student, 422);
        $student->load('classRoom');

        $payments = Payment::with(['bill.feeCategory'])
            ->where('student_id', $student->id)
            ->orderByDesc('paid_at')
            ->get();
        $school = \App\Models\SchoolSetting::pluck('value', 'key')->toArray();
        $totalSukses = (float) $payments->where('status', 'success')->sum('amount');

        return response(view('portal.cetak-riwayat', [
            'student' => $student,
            'payments' => $payments,
            'school' => $school,
            'totalSukses' => $totalSukses,
            'terbilang' => ReceiptService::terbilang($totalSukses),
        ])->render(), 200, ['Content-Type' => 'text/html']);
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
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Midtrans webhook endpoint is ready and accepting POST requests.',
            ], 200);
        }

        $payload = $request->all();
        Log::info('Midtrans callback incoming:', [
            'order_id' => $payload['order_id'] ?? null,
            'status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
            'gross_amount' => $payload['gross_amount'] ?? null,
        ]);

        abort_unless($midtrans->verifyCallback($payload), 422, 'Callback tidak valid.');

        $bill = $this->findBillForOrder($payload['order_id'] ?? '');
        if (! $bill) {
            Log::info('Midtrans callback order tidak ditemukan (test/sample notification): ' . ($payload['order_id'] ?? '-'));

            return response()->json([
                'status' => 'ok',
                'message' => 'Tagihan tidak ditemukan di database. Notifikasi diakui (test event).',
            ], 200);
        }

        $status = $payload['transaction_status'] ?? '';

        if (in_array($status, ['settlement', 'capture'])) {
            $payment = $this->settleBillFromPayload($bill, $payload);

            return response()->json(['message' => 'OK', 'payment_id' => $payment->id]);
        }

        if (in_array($status, ['expire', 'cancel', 'deny'])) {
            BillEvent::create(['bill_id' => $bill->id, 'event' => 'payment_' . $status, 'message' => 'Midtrans callback: ' . $status]);

            return response()->json(['message' => 'Callback dicatat.']);
        }

        return response()->json(['message' => 'Status diabaikan.']);
    }

    /** Cek status langsung ke Midtrans; lunasi otomatis bila ternyata sudah dibayar. */
    public function checkStatus(Request $request, Bill $bill, MidtransService $midtrans)
    {
        $student = $this->student();
        abort_if(! $student || $bill->student_id !== $student->id, 403);
        abort_if(! $bill->order_id, 422, 'Belum ada transaksi berjalan untuk tagihan ini.');

        try {
            $remote = $midtrans->transactionStatus($bill->order_id);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        $status = $remote['transaction_status'] ?? 'unknown';
        if (in_array($status, ['settlement', 'capture']) && $bill->status !== 'paid') {
            $this->settleBillFromPayload($bill, $remote);

            return response()->json(['settled' => true, 'status' => $status]);
        }

        return response()->json(['settled' => $bill->status === 'paid', 'status' => $status]);
    }

    /** Cari tagihan via order_id persis, fallback ke bill_code (order lama setelah klik Bayar ulang). */
    protected function findBillForOrder(string $orderId): ?Bill
    {
        $bill = Bill::where('order_id', $orderId)->first();
        if ($bill || ! str_starts_with($orderId, 'SS-')) {
            return $bill;
        }
        // Suffix percobaan (-2, -3, …) dikupas satu per satu; kode tagihan sendiri
        // boleh mengandung angka (mis. INV-…-0001) sehingga coba utuh dulu.
        $rem = substr($orderId, 3);
        while ($rem !== '') {
            $bill = Bill::where('bill_code', $rem)->first();
            if ($bill) {
                return $bill;
            }
            $next = preg_replace('/-\d+$/', '', $rem);
            if ($next === $rem) {
                break; // tak ada suffix angka lagi
            }
            $rem = $next;
        }

        return null;
    }

    /** Lunasi total pokok + denda dari payload Midtrans (dipakai callback & cek status). */
    protected function settleBillFromPayload(Bill $bill, array $payload): Payment
    {
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

        BillEvent::create(['bill_id' => $bill->id, 'event' => 'settlement', 'message' => 'Settlement otomatis order ' . ($payload['order_id'] ?? '-') . ' total Rp ' . number_format($amount, 0, ',', '.') . ' (pokok + denda)']);

        return $payment;
    }
}
