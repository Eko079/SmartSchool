<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\User;
use App\Services\WaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBillReminders extends Command
{
    protected $signature = 'bills:remind';

    protected $description = 'Kirim pengingat H-3/H-1/H0 jatuh tempo + notifikasi saat baru menunggak via WA (log).';

    public function handle(WaService $wa): int
    {
        $today = Carbon::today();
        $sent = 0;

        // Pengingat sebelum jatuh tempo: H-3, H-1, H-0 untuk status belum lunas.
        $upcoming = Bill::with('student')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', $today->copy()->addDays(3))
            ->get();

        foreach ($upcoming as $bill) {
            $daysLeft = (int) $today->diffInDays(Carbon::parse($bill->due_date)->startOfDay(), false);
            $template = $daysLeft <= 0 ? 'reminder_due_today' : "reminder_h-{$daysLeft}";
            // Batasi ke H-3, H-1, H-0 agar tidak spam tiap hari.
            if (! in_array($daysLeft, [3, 1, 0], true)) {
                continue;
            }
            // Jangan kirim ulang template sama di hari yang sama.
            $already = BillEvent::where('bill_id', $bill->id)
                ->where('event', 'wa_sent')
                ->where('message', 'like', "%{$template}%")
                ->whereDate('created_at', $today)
                ->exists();
            if ($already) {
                continue;
            }
            $user = User::where('student_id', $bill->student_id)
                ->where('notify_wa', true)
                ->whereNotNull('phone')
                ->first();
            $dest = $user?->phone ?: ($bill->student->guardian_phone ?? null);
            if (! $dest) {
                continue;
            }
            $total = number_format($bill->total_due, 0, ',', '.');
            $wa->send($user?->id, $bill->id, $dest, $template,
                "Pengingat {$bill->bill_code} jatuh tempo {$bill->due_date->format('d M Y')} (H-{$daysLeft}), total Rp {$total} via template {$template}");
            $sent++;
        }

        $this->info("Pengingat terkirim: {$sent}.");

        return self::SUCCESS;
    }
}
