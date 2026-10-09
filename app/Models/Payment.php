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
}
