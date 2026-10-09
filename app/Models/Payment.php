<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'bill_id',
        'student_id',
        'amount',
        'payment_method',
        'gateway',
        'transaction_id',
        'va_number',
        'expiry_at',
        'settlement_at',
        'callback_payload',
        'receipt_path',
        'status',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expiry_at' => 'datetime',
        'settlement_at' => 'datetime',
        'callback_payload' => 'array',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public static function methodLabel(?string $method): string
    {
        if (! $method) {
            return '-';
        }
        $map = [
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'qris' => 'QRIS Dinamis',
            'bank_transfer' => 'Transfer Bank',
            'echannel' => 'Mandiri Bill',
            'cstore' => 'Gerai Retail',
            'midtrans' => 'Midtrans',
        ];

        return $map[strtolower(trim($method))] ?? $method;
    }

    public function getMethodLabelAttribute(): string
    {
        return self::methodLabel($this->payment_method);
    }

    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            'success' => 'LUNAS',
            'pending' => 'MENUNGGU',
            'failed' => 'GAGAL',
            default => strtoupper((string) $this->status),
        };
    }

    public static function prefixForCategory(?string $code): string
    {
        $code = strtoupper(trim((string) $code));
        if (str_starts_with($code, 'PSAJ')) {
            return 'PSJ';
        }
        $alnum = preg_replace('/[^A-Z0-9]/', '', $code);

        return substr(str_pad($alnum, 3, 'X'), 0, 3);
    }

    public static function nextInvoiceNumber(?string $categoryCode = null): string
    {
        $prefix = self::prefixForCategory($categoryCode);
        $max = 0;
        foreach (self::where('invoice_number', 'like', 'KW-%')->pluck('invoice_number') as $inv) {
            if (preg_match('/KW-[A-Z0-9]{3}-(\d+)$/', $inv, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }
        $next = $max > 0 ? $max + 1 : (int) (self::max('id') ?? 0) + 1;
        do {
            $candidate = sprintf('KW-%s-%04d', $prefix, $next);
            $next++;
        } while (self::where('invoice_number', $candidate)->exists());

        return $candidate;
    }
}
