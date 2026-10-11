<?php

namespace App\Services;

use App\Models\Payment;

class ReceiptService
{
    public function receiptText(Payment $payment): string
    {
        $payment->loadMissing(['student', 'bill.feeCategory']);
        $fine = (float) ($payment->bill->fine_amount ?? 0);
        $principal = max(0, (float) $payment->amount - $fine);
        $lines = [
            'SMARTSCHOOL - KUITANSI PEMBAYARAN',
            'Invoice: ' . $payment->invoice_number,
            'Siswa: ' . ($payment->student->name ?? '-'),
            'Kategori: ' . ($payment->bill->display_name ?? '-'),
            'Pokok: Rp ' . number_format($principal, 0, ',', '.'),
            'Denda: Rp ' . number_format($fine, 0, ',', '.'),
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
            'terbilang' => self::terbilang((float) $payment->amount),
        ])->render();
    }

    /** Nominal menjadi kata Bahasa Indonesia ("satu juta rupiah"). */
    public static function terbilang(float $number): string
    {
        $n = (int) round($number);
        if ($n < 0) {
            return 'minus ' . self::terbilang(-$n);
        }
        if ($n === 0) {
            return 'nol rupiah';
        }
        $kata = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $sebut = function ($x) use (&$sebut, $kata): string {
            if ($x < 12) {
                return $kata[$x];
            }
            if ($x < 20) {
                return $sebut($x - 10) . ' belas';
            }
            if ($x < 100) {
                return trim($sebut(intdiv($x, 10)) . ' puluh ' . $sebut($x % 10));
            }
            if ($x < 200) {
                return trim('seratus ' . $sebut($x - 100));
            }
            if ($x < 1000) {
                return trim($sebut(intdiv($x, 100)) . ' ratus ' . $sebut($x % 100));
            }
            if ($x < 2000) {
                return trim('seribu ' . $sebut($x - 1000));
            }
            if ($x < 1000000) {
                return trim($sebut(intdiv($x, 1000)) . ' ribu ' . $sebut($x % 1000));
            }
            if ($x < 1000000000) {
                return trim($sebut(intdiv($x, 1000000)) . ' juta ' . $sebut($x % 1000000));
            }
            if ($x < 1000000000000) {
                return trim($sebut(intdiv($x, 1000000000)) . ' miliar ' . $sebut($x % 1000000000));
            }

            return trim($sebut(intdiv($x, 1000000000000)) . ' triliun ' . $sebut($x % 1000000000000));
        };

        return trim(preg_replace('/\s+/', ' ', $sebut($n))) . ' rupiah';
    }
}
