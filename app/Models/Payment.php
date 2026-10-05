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
}
