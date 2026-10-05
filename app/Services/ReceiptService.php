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
            'Metode: ' . $payment->payment_method,
            'Waktu: ' . ($payment->paid_at?->format('d M Y H:i') ?? '-'),
            'Status: ' . $payment->status,
        ];

        return implode("\n", $lines);
    }
}
