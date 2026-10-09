<?php

namespace App\Services;

use App\Models\Payment;

class ReceiptService
{
    public function receiptText(Payment $payment): string
    {
        $payment->loadMissing(['student', 'bill.feeCategory']);
        $lines = [
            'SMARTSCHOOL - KUITANSI PEMBAYARAN',
            'Invoice: ' . $payment->invoice_number,
            'Siswa: ' . ($payment->student->name ?? '-'),
            'Kategori: ' . ($payment->bill->feeCategory->name ?? '-'),
            'Jumlah: Rp ' . number_format((float) $payment->amount, 0, ',', '.'),
            'Metode: ' . $payment->method_label,
            'Waktu: ' . ($payment->paid_at?->format('d M Y H:i') ?? '-'),
            'Status: ' . $payment->status,
        ];

        return implode("\n", $lines);
    }

    public function receiptHtml(Payment $payment): string
    {
        $payment->loadMissing(['student.classRoom', 'bill.feeCategory']);
        $school = \App\Models\SchoolSetting::pluck('value', 'key')->toArray();
        $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

        return view('portal.kuitansi', [
            'pay' => $payment,
            'school' => $school,
            'rp' => $rp,
        ])->render();
    }
}
