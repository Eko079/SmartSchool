<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MidtransService
{
    public function serverKey(): string
    {
        return trim((string) SchoolSetting::get('midtrans_server_key', ''));
    }

    public function isSandbox(): bool
    {
        return (string) SchoolSetting::get('midtrans_sandbox', '1') === '1';
    }

    public function baseUrl(): string
    {
        return $this->isSandbox() ? 'https://api.sandbox.midtrans.com' : 'https://api.midtrans.com';
    }

    /** Key asli terpasang? Nilai seed demo dianggap belum dikonfigurasi. */
    public function isConfigured(): bool
    {
        $key = $this->serverKey();
        if ($key === '' || strlen($key) < 10) {
            return false;
        }
        if (stripos($key, 'demo') !== false) {
            return false;
        }

        return true;
    }

    public static function gatewaySettingKey(string $method): ?string
    {
        return match ($method) {
            'bca_va' => 'gateway_bca_va',
            'bni_va' => 'gateway_bni_va',
            'qris' => 'gateway_qris',
            default => null,
        };
    }

    public function isMethodEnabled(string $method): bool
    {
        $key = self::gatewaySettingKey($method);
        if (! $key) {
            return false;
        }

        return (string) SchoolSetting::get($key, '1') === '1';
    }

    /** Order ID unik per percobaan: SS-{bill_code}, lalu -2, -3, … (anti 406 duplikat). */
    public function nextOrderId(Bill $bill): string
    {
        $base = 'SS-' . $bill->bill_code;
        $cur = (string) ($bill->order_id ?? '');
        if ($cur === '' || ! str_starts_with($cur, $base)) {
            return $base;
        }
        if ($cur === $base) {
            return $base . '-2';
        }
        if (preg_match('/-(\d+)$/', $cur, $m)) {
            return $base . '-' . ((int) $m[1] + 1);
        }

        return $base . '-2';
    }

    public function createTransaction(Bill $bill, string $method = 'bca_va'): array
    {
        // Segarkan denda terkini sebelum transaksi agar gross = sisa pokok + denda.
        $calc = Bill::calcFine($bill->due_date);
        if ((float) $bill->fine_amount !== (float) $calc['fine'] && $bill->status !== 'paid') {
            $bill->forceFill(['fine_amount' => $calc['fine']])->save();
            $bill->refresh();
        }
        $gross = max(0, (int) round($bill->total_due));
        $orderId = $this->nextOrderId($bill);

        // Belum ada key asli (dev/demo): simulasi lokal seperti sebelumnya.
        if (! $this->isConfigured()) {
            return $this->simulatedTransaction($method, $orderId, $gross, $bill);
        }

        $bill->loadMissing(['student', 'feeCategory']);
        $params = [
            'payment_type' => $method === 'qris' ? 'qris' : 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $gross,
            ],
            'customer_details' => array_filter([
                'first_name' => mb_substr((string) ($bill->student->name ?? 'Wali'), 0, 20),
                'phone' => $bill->student->guardian_phone ?? null,
            ]),
            'item_details' => [[
                'id' => mb_substr((string) $bill->bill_code, 0, 20),
                'price' => $gross,
                'quantity' => 1,
                'name' => mb_substr((string) ($bill->display_name ?: $bill->bill_code), 0, 50),
            ]],
        ];
        if ($method !== 'qris') {
            $params['bank_transfer'] = ['bank' => $method === 'bni_va' ? 'bni' : 'bca'];
        }

        // Midtrans bisa balas HTTP 200 berisi status_code 406 (order duplikat):
        // wajib cek isi body, lalu coba suffix berikut (-2, -3, …) maksimal 5x.
        $data = [];
        for ($i = 0; $i < 5; $i++) {
            $params['transaction_details']['order_id'] = $orderId;
            $resp = Http::withBasicAuth($this->serverKey(), '')
                ->acceptJson()->timeout(25)
                ->post($this->baseUrl() . '/v2/charge', $params);

            if (! $resp->successful()) {
                $msg = $resp->json('status_message') ?? $resp->json('message') ?? $resp->body();
                throw new \RuntimeException('Midtrans menolak transaksi: ' . $msg);
            }
            $data = $resp->json() ?? [];
            $code = (string) ($data['status_code'] ?? '');
            if ($code === '200' || $code === '201') {
                break;
            }
            $msg = (string) ($data['status_message'] ?? '');
            if ($code === '406' || stripos($msg, 'conflict') !== false) {
                $bill->order_id = $orderId; // sementara (belum disimpan) agar nextOrderId naik
                $orderId = $this->nextOrderId($bill);
                $data = [];
                continue;
            }
            throw new \RuntimeException('Midtrans menolak transaksi: ' . ($msg ?: $resp->body()));
        }
        if (empty($data)) {
            throw new \RuntimeException('Midtrans menolak transaksi: order duplikat berulang, coba lagi.');
        }
        $expired = now()->addHours(24);
        if (! empty($data['expiry_time'])) {
            try {
                $expired = \Carbon\Carbon::parse($data['expiry_time']);
            } catch (\Throwable) {
            }
        }

        return [
            'order_id' => $orderId,
            'va_number' => $data['va_numbers'][0]['va_number'] ?? null,
            'qr_string' => $data['qr_string'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'gateway' => 'midtrans',
            'simulated' => false,
            'payment_method' => $method,
            'gross_amount' => $gross,
            'expired_at' => $expired,
        ];
    }

    protected function simulatedTransaction(string $method, string $orderId, int $gross, Bill $bill): array
    {
        $isQris = $method === 'qris';

        return [
            'order_id' => $orderId,
            'va_number' => match ($method) {
                'bni_va' => '8002' . str_pad((string) $bill->id, 8, '0', STR_PAD_LEFT),
                'qris' => null,
                default => '8001' . str_pad((string) $bill->id, 8, '0', STR_PAD_LEFT),
            },
            'qr_string' => $isQris ? 'SIMULASI-QRIS:' . $orderId : null,
            'transaction_id' => 'SIM-' . strtoupper(Str::random(10)),
            'gateway' => 'midtrans',
            'simulated' => true,
            'payment_method' => $method,
            'gross_amount' => $gross,
            'expired_at' => now()->addHours(24),
        ];
    }

    /** Verifikasi signature_key SHA512 resmi Midtrans (mode simulasi: cek longgar). */
    public function verifyCallback(array $payload): bool
    {
        if (! isset($payload['order_id']) || ! isset($payload['transaction_status'])) {
            return false;
        }
        if (! $this->isConfigured()) {
            return true;
        }
        $expected = hash('sha512', ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $this->serverKey());

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    /** Tanya status transaksi langsung ke Midtrans (tombol "Cek Status"). */
    public function transactionStatus(string $orderId): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Kunci Midtrans belum dipasang, status tidak bisa dicek.');
        }
        $resp = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()->timeout(20)
            ->get($this->baseUrl() . '/v2/' . $orderId . '/status');
        if (! $resp->successful()) {
            $msg = $resp->json('status_message') ?? $resp->json('message') ?? $resp->body();
            throw new \RuntimeException('Gagal cek status: ' . $msg);
        }

        return $resp->json();
    }
}
