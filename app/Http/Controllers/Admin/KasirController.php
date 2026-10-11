<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /** Cari siswa + tampilkan tagihan belum lunasnya. */
    public function index(Request $request)
    {
        $students = collect();
        if ($request->filled('q')) {
            $q = $request->input('q');
            $students = Student::with('classRoom')
                ->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('nis', 'like', "%{$q}%")
                        ->orWhere('nisn', 'like', "%{$q}%");
                })
                ->orderBy('name')->take(10)->get();
        }

        $student = null;
        $bills = collect();
        if ($request->filled('student_id')) {
            $student = Student::with('classRoom')->find($request->input('student_id'));
            if ($student) {
                $bills = Bill::with('feeCategory')
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                    ->orderBy('sort_order')->orderBy('id')->get()
                    ->each(function ($b) {
                        // Denda dihitung ulang saat tampil agar kasir tagih angka terkini.
                        $calc = Bill::calcFine($b->due_date);
                        $b->fresh_fine = $calc['fine'];
                        $b->fresh_total = max(0, (float) $b->amount - (float) $b->paid_amount + (float) $calc['fine']);
                    });
            }
        }

        return view('admin.kasir.index', compact('students', 'student', 'bills'));
    }

    /** Lunasi penuh tagihan terpilih. Satu Payment per tagihan (kuitansi per tagihan). */
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'integer',
            'method' => 'required|in:tunai,transfer',
            'cash' => 'nullable|numeric|min:0',
        ]);
        $student = Student::findOrFail($validated['student_id']);
        $kasir = auth()->user()->name ?? 'Kasir';

        // Pratinjau total dulu agar uang tunai bisa divalidasi SEBELUM ada yang tercatat.
        $preview = Bill::where('student_id', $student->id)
            ->whereIn('id', $validated['bill_ids'])
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])->get()
            ->sum(fn ($b) => max(0, (float) $b->amount - (float) $b->paid_amount + (float) Bill::calcFine($b->due_date)['fine']));
        abort_if($preview <= 0, 422, 'Tidak ada tagihan yang bisa dibayar.');
        $cash = $validated['method'] === 'tunai' ? (float) ($validated['cash'] ?? 0) : null;
        if ($validated['method'] === 'tunai') {
            $request->validate(['cash' => 'required|numeric|min:0']);
            abort_if($cash < $preview, 422, 'Uang diterima kurang dari total.');
        }

        $result = DB::transaction(function () use ($validated, $student, $kasir, $cash) {
            $total = 0;
            $ids = [];
            $bills = Bill::with('feeCategory')->where('student_id', $student->id)
                ->whereIn('id', $validated['bill_ids'])
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->lockForUpdate()->get();
            abort_if($bills->isEmpty(), 422, 'Tidak ada tagihan yang bisa dibayar.');

            foreach ($bills as $bill) {
                $calc = Bill::calcFine($bill->due_date);
                $due = max(0, (float) $bill->amount - (float) $bill->paid_amount + (float) $calc['fine']);
                if ($validated['method'] === 'tunai' && $cash < $total + $due) {
                    abort(422, 'Uang diterima kurang dari total.');
                }
                $payment = Payment::create([
                    'invoice_number' => Payment::nextInvoiceNumber($bill->feeCategory->code ?? null),
                    'bill_id' => $bill->id,
                    'student_id' => $bill->student_id,
                    'amount' => $due,
                    'payment_method' => $validated['method'],
                    'gateway' => 'kasir',
                    'settlement_at' => now(),
                    'status' => 'success',
                    'paid_at' => now(),
                    'note' => 'Kasir (' . $validated['method'] . ') oleh ' . $kasir,
                ]);
                $bill->forceFill([
                    'paid_amount' => $bill->amount,
                    'fine_amount' => $calc['fine'],
                    'status' => 'paid',
                ])->save();
                BillEvent::create(['bill_id' => $bill->id, 'event' => 'kasir',
                    'message' => 'Bayar di kasir (' . $validated['method'] . ') oleh ' . $kasir . ' total Rp ' . number_format($due, 0, ',', '.')]);
                $total += $due;
                $ids[] = $payment->id;
            }

            return ['total' => $total, 'count' => count($ids), 'ids' => $ids];
        });

        \App\Models\AuditLog::log('kasir.bayar', "Kasir {$validated['method']}: {$student->name} {$result['count']} tagihan Rp " . number_format($result['total'], 0, ',', '.'));

        return redirect()->route('admin.kasir.struk', array_filter([
            'ids' => $result['ids'],
            'cash' => $cash,
        ]))->with('success', "Lunas {$result['count']} tagihan ({$student->name}) Rp " . number_format($result['total'], 0, ',', '.'));
    }

    /** Struk hasil pembayaran kasir + tombol cetak. */
    public function struk(Request $request)
    {
        $ids = array_values(array_filter((array) $request->input('ids', []), fn ($v) => is_numeric($v)));
        $payments = Payment::with(['bill.feeCategory', 'student.classRoom'])
            ->whereIn('id', $ids)->where('gateway', 'kasir')->orderBy('id')->get();
        abort_if($payments->isEmpty(), 404);
        $total = (float) $payments->sum('amount');
        $cash = $request->filled('cash') ? (float) $request->input('cash') : null;

        return view('admin.kasir.struk', compact('payments', 'total', 'cash'));
    }
}
