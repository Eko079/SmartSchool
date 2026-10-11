<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'default_amount',
        'is_active',
        'description',
        'semester',
        'majors',
        'grades',
        'audience',
        'due_month',
        'due_day',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_amount' => 'decimal:2',
        'majors' => 'array',
        'grades' => 'array',
    ];

    /** Ringkasan ramah tampil, mis. "Ganjil · IPA · Kelas X · Siswa baru". */
    public function getTargetSummaryAttribute(): string
    {
        $parts = [];
        $parts[] = $this->semester === 'ganjil' ? 'Ganjil' : ($this->semester === 'genap' ? 'Genap' : 'Dua semester');
        $parts[] = empty($this->majors) ? 'Semua jurusan' : implode('/', (array) $this->majors);
        $parts[] = empty($this->grades) ? 'Semua kelas' : 'Kelas '.implode('/', (array) $this->grades);
        if (($this->audience ?? null) === 'baru') {
            $parts[] = 'Siswa baru';
        } elseif (($this->audience ?? null) === 'lama') {
            $parts[] = 'Siswa lama';
        }

        return implode(' · ', $parts);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'fee_category_id');
    }
}
