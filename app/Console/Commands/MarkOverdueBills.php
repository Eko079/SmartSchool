<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkOverdueBills extends Command
{
    protected $signature = 'bills:mark-overdue';

    protected $description = 'Tandai tagihan lewat jatuh tempo sebagai overdue + hitung denda dari Pengaturan.';

    public function handle(): int
    {
        $perDay = (float) SchoolSetting::get('fine_per_day', 0);
        $max = (float) SchoolSetting::get('fine_max', 0);
        $today = Carbon::today();

        $bills = Bill::whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->get();

        $count = 0;
        foreach ($bills as $bill) {
            $daysLate = $today->diffInDays(Carbon::parse($bill->due_date), false);
            $daysLate = max(0, (int) $daysLate);
            $fine = $perDay > 0 ? min($daysLate * $perDay, $max > 0 ? $max : PHP_FLOAT_MAX) : 0;

            $bill->forceFill([
                'status' => 'overdue',
                'fine_amount' => $fine,
            ])->save();

            BillEvent::create([
                'bill_id' => $bill->id,
                'event' => 'overdue',
                'message' => "Terlambat {$daysLate} hari, denda Rp " . number_format($fine, 0, ',', '.'),
            ]);
            $count++;
        }

        $this->info("Ditandai overdue: {$count} tagihan.");

        return self::SUCCESS;
    }
}
