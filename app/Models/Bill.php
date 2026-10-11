<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\PackageRules;

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
        'sort_order',
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

    /** Nama ramah tampil: "SPP Bulan N" untuk SPP, nama kategori untuk lainnya. */
    public function getDisplayNameAttribute(): string
    {
        return PackageRules::displayName(
            $this->feeCategory->code ?? '',
            $this->feeCategory->name,
            $this->period_month !== null ? (int) $this->period_month : null
        );
    }

    /** Total wajib bayar: sisa pokok + denda. */
    public function getTotalDueAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount + (float) $this->fine_amount);
    }

    /** Sisa pokok belum dibayar (tanpa denda). */
    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    /** Teks jatuh tempo: tanggal + sisa/lewat hari. */
    public function getDueInfoAttribute(): string
    {
        if (! $this->due_date) {
            return '-';
        }
        $dl = (int) \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($this->due_date->format('Y-m-d')), false);
        $base = $this->due_date->format('d M Y');
        if ($dl < 0) {
            return $base.' • lewat '.abs($dl).' hari';
        }
        if ($dl === 0) {
            return $base.' • hari ini';
        }

        return $base.' • '.$dl.' hari lagi';
    }

    /** Hitung denda dari Pengaturan: per-hari x hari telat lewat grace, dibatasi max. */
    public static function calcFine(string|object|null $dueDate, ?string $today = null): array
    {
        $perDay = (float) \App\Models\SchoolSetting::get('fine_per_day', 0);
        $max = (float) \App\Models\SchoolSetting::get('fine_max', 0);
        $grace = (int) \App\Models\SchoolSetting::get('grace_days', 0);
        if (! $dueDate || $perDay <= 0) {
            return ['days_late' => 0, 'fine' => 0.0];
        }
        $due = $dueDate instanceof \DateTimeInterface ? \Carbon\Carbon::parse($dueDate->format('Y-m-d')) : \Carbon\Carbon::parse($dueDate)->startOfDay();
        $now = $today ? \Carbon\Carbon::parse($today)->startOfDay() : \Carbon\Carbon::today();
        $daysLate = max(0, (int) $due->diffInDays($now, false));
        $billable = max(0, $daysLate - $grace);
        $fine = $billable > 0 ? $billable * $perDay : 0;
        if ($max > 0) {
            $fine = min($fine, $max);
        }

        return ['days_late' => $daysLate, 'fine' => (float) $fine];
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
