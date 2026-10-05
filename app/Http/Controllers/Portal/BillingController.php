<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
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
    protected function student()
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();

        return $user->student;
    }

    protected function scopeOwned(Request $request)
    {
        $student = $this->student();
        abort_if(! $student, 422, 'Siswa tidak tertaut ke akun wali.');

        return Bill::with(['feeCategory', 'student.classRoom'])->where('student_id', $student->id);
    }

    public function index(Request $request)
    {
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

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 50 ? $perPage : 10;

        $bills = $query->orderByDesc('due_date')->paginate($perPage)->withQueryString();
        $summary = [
            'waiting' => (float) (clone $query)->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('amount'),
            'paid' => (float) (clone $query)->where('status', 'paid')->sum('amount'),
        ];

        if ($request->expectsJson() || ! view()->exists('portal.tagihan')) {
            return response()->json(['bills' => $bills, 'summary' => $summary]);
        }

        return view('portal.tagihan', compact('bills', 'summary'));
    }

    public function show(Request $request, Bill $bill)
    {
        $student = $this->student();
        abort_if(! $student || $bill->student_id !== $student->id, 403);

        $bill->load(['feeCategory', 'student.classRoom', 'payments', 'events']);

        if ($request->expectsJson() || ! view()->exists('portal.tagihan-detail')) {
            return response()->json(['bill' => $bill]);
        }

        return view('portal.tagihan-detail', compact('bill'));
    }

    public function pay(Request $request, Bill $bill, MidtransService $midtrans, WaService $wa)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:bca_va,bni_va,qris'],
        ]);

        $student = $this->student();
        abort_if(! $student || $bill->student_id !== $student->id, 403);
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

        return view('portal.riwayat', compact('payments'));
    }

    public function receipt(Request $request, Payment $payment, ReceiptService $receipts)
    {
        $student = $this->student();
        abort_if(! $student || $payment->student_id !== $student->id, 403);

        return response($receipts->receiptText($payment), 200, ['Content-Type' => 'text/plain']);
    }

    public function callback(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();
        abort_unless($midtrans->verifyCallback($payload), 422, 'Callback tidak valid.');

        $bill = Bill::where('order_id', $payload['order_id'])->firstOrFail();
        $status = $payload['transaction_status'] ?? '';

        if (in_array($status, ['settlement', 'capture'])) {
            $amount = (float) ($payload['gross_amount'] ?? $bill->amount);
            $payment = Payment::updateOrCreate(
                ['invoice_number' => 'PAY-' . $payload['order_id']],
                [
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
                ]
            );
            $bill->forceFill([
                'paid_amount' => $bill->amount,
                'status' => 'paid',
            ])->save();

            BillEvent::create(['bill_id' => $bill->id, 'event' => 'settlement', 'message' => 'Settlement otomatis order ' . $payload['order_id']]);

            return response()->json(['message' => 'OK', 'payment_id' => $payment->id]);
        }

        if (in_array($status, ['expire', 'cancel', 'deny'])) {
            BillEvent::create(['bill_id' => $bill->id, 'event' => 'payment_' . $status, 'message' => 'Midtrans callback: ' . $status]);

            return response()->json(['message' => 'Callback dicatat.']);
        }

        return response()->json(['message' => 'Status diabaikan.']);
    }
}
