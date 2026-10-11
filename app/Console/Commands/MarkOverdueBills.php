<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Services\WaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkOverdueBills extends Command
{
    protected $signature = 'bills:mark-overdue';

    protected $description = 'Tandai tagihan lewat jatuh tempo sebagai overdue + hitung denda dari Pengaturan.';

    public function handle(WaService $wa): int
    {
        $today = Carbon::today();
        $grace = (int) SchoolSetting::get('grace_days', 0);

        $bills = Bill::whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $today)
            ->get();

        $count = 0;
        foreach ($bills as $bill) {
            $calc = Bill::calcFine($bill->due_date);
            $daysLate = $calc['days_late'];
            $fine = $calc['fine'];

            // Belum lewat toleransi: jangan tandai overdue.
            if ($daysLate <= $grace) {
                // Koreksi denda bila sebelumnya terlanjur ada.
                if ((float) $bill->fine_amount !== 0.0) {
                    $bill->forceFill(['fine_amount' => 0])->save();
                }
                continue;
            }

            $wasOverdue = $bill->status === 'overdue';
            $fineChanged = abs((float) $bill->fine_amount - $fine) > 0.01;

            $bill->forceFill([
                'status' => 'overdue',
                'fine_amount' => $fine,
            ])->save();

            // Idempotent: catat event hanya saat status baru berubah atau nominal denda berubah.
            if (! $wasOverdue || $fineChanged) {
                BillEvent::create([
                    'bill_id' => $bill->id,
                    'event' => 'overdue',
                    'message' => "Terlambat {$daysLate} hari (toleransi {$grace} hari), denda Rp " . number_format($fine, 0, ',', '.'),
                ]);

                // Notifikasi WA sekali saat baru menjadi overdue.
                if (! $wasOverdue) {
                    $user = User::where('student_id', $bill->student_id)
                        ->where('notify_wa', true)
                        ->whereNotNull('phone')
                        ->first();
                    $bill->loadMissing('student');
                    $dest = $user?->phone ?: ($bill->student->guardian_phone ?? null);
                    if ($dest) {
                        $wa->send($user?->id, $bill->id, $dest, 'overdue',
                            "Tagihan {$bill->bill_code} menunggak {$daysLate} hari, total Rp " . number_format($bill->fresh()->total_due, 0, ',', '.') . ' via template overdue');
                    }
                }
            }
            $count++;
        }

        $this->info("Ditandai overdue: {$count} tagihan.");

        return self::SUCCESS;
    }
}
