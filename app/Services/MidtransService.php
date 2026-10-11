<?php

namespace App\Services;

use App\Models\Bill;
use Illuminate\Support\Str;

class MidtransService
{
    public function createTransaction(Bill $bill, string $method = 'bca_va'): array
    {
        // Segarkan denda terkini sebelum transaksi agar gross = sisa pokok + denda.
        $calc = Bill::calcFine($bill->due_date);
        if ((float) $bill->fine_amount !== (float) $calc['fine'] && $bill->status !== 'paid') {
            $bill->forceFill(['fine_amount' => $calc['fine']])->save();
            $bill->refresh();
        }
        $gross = $bill->total_due;
        $orderId = $bill->order_id ?: ('SS-' . $bill->bill_code);
        $vaNumber = match ($method) {
            'bni_va' => '8002' . str_pad((string) $bill->id, 8, '0', STR_PAD_LEFT),
            'qris' => 'QRIS-' . strtoupper(Str::random(10)),
            default => '8001' . str_pad((string) $bill->id, 8, '0', STR_PAD_LEFT),
        };

        return [
            'order_id' => $orderId,
            'va_number' => $vaNumber,
            'gateway' => 'midtrans',
            'payment_method' => $method,
            'gross_amount' => $gross,
            'expired_at' => now()->addHours(24),
        ];
    }

    public function verifyCallback(array $payload): bool
    {
        return isset($payload['order_id']) && isset($payload['transaction_status']);
    }
}
