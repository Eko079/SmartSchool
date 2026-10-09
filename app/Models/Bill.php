<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_code',
        'order_id',
        'student_id',
        'fee_category_id',
        'period_month',
        'period_year',
        'academic_year',
        'semester',
        'amount',
        'paid_amount',
        'fine_amount',
        'va_number',
        'qris_payload',
        'expired_at',
        'status',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'due_date' => 'date',
        'expired_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    public function events()
    {
        return $this->hasMany(BillEvent::class, 'bill_id');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'LUNAS',
            'partial' => 'SEBAGIAN',
            'overdue' => 'MENUNGGAK',
            default => 'BELUM BAYAR',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function getDisplayYearAttribute(): ?string
    {
        if ($this->academic_year) {
            return $this->academic_year;
        }
        if (preg_match('/(\d{4}\s*\/\s*\d{4})/', $this->feeCategory?->name ?? '', $m)) {
            return str_replace(' ', '', $m[1]);
        }

        return null;
    }
}
