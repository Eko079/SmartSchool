<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'event',
        'message',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function getFriendlyAttribute(): string
    {
        $m = (string) ($this->message ?? $this->event ?? '');

        if (preg_match('/^Pembayaran dimulai via (\S+)/', $m, $mm)) {
            return 'Pembayaran via ' . Payment::methodLabel($mm[1]) . ' dimulai';
        }
        if (preg_match('/^WA terkirim ke (\S+)/', $m, $mm)) {
            return 'Notifikasi WA terkirim ke ' . $mm[1];
        }
        if (str_starts_with($m, 'Settlement otomatis')) {
            return 'Pembayaran terkonfirmasi otomatis (lunas)';
        }

        return $m;
    }
}
