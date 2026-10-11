<?php

namespace App\Support;

use App\Models\Bill;
use App\Models\BillEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Notifications
{
    protected static ?array $memo = null;

    /** Feed popup bell: pengingat jatuh tempo + denda menunggak. Maks 8 item terbaru. */
    public static function feed(): array
    {
        // Memo per-request: composer '*' jalan tiap partial, jangan query berulang.
        if (self::$memo !== null) {
            return self::$memo;
        }
        try {
            $user = Auth::guard('wali')->user() ?? Auth::user();
            if (! $user) {
                return ['count' => 0, 'items' => []];
            }

            $isWali = ($user->role ?? null) === 'wali' || $user->student_id;
            $today = Carbon::today();
            $items = [];

            if ($isWali && $user->student_id) {
                $bills = Bill::with('feeCategory')
                    ->where('student_id', $user->student_id)
                    ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                    ->orderBy('due_date')
                    ->take(20)
                    ->get();

                foreach ($bills as $b) {
                    $due = $b->due_date ? Carbon::parse($b->due_date->format('Y-m-d')) : null;
                    $daysLeft = $due ? (int) $today->diffInDays($due, false) : null;
                    $total = number_format($b->total_due, 0, ',', '.');
                    $cat = $b->feeCategory->name ?? 'Tagihan';

                    if ($b->status === 'overdue') {
                        $items[] = [
                            'kind' => 'overdue',
                            'title' => "Menunggak: {$cat}",
                            'desc' => "{$b->bill_code} • total Rp {$total} (termasuk denda Rp " . number_format($b->fine_amount, 0, ',', '.') . ')',
                            'url' => route('portal.tagihan.show', $b->id),
                            'ts' => $b->updated_at?->timestamp ?? 0,
                        ];
                    } elseif ($daysLeft !== null && $daysLeft <= 3) {
                        $when = $daysLeft <= 0 ? 'jatuh tempo hari ini' : "H-{$daysLeft} jatuh tempo";
                        $items[] = [
                            'kind' => 'reminder',
                            'title' => "Pengingat: {$cat}",
                            'desc' => "{$b->bill_code} • {$when} • Rp {$total}",
                            'url' => route('portal.tagihan.show', $b->id),
                            'ts' => $b->due_date?->timestamp ?? 0,
                        ];
                    }
                }
            } else {
                // Admin: ringkasan menunggak + pengingat 3 hari ke depan.
                $overdue = Bill::with(['student', 'feeCategory'])
                    ->where('status', 'overdue')
                    ->latest('updated_at')
                    ->take(5)
                    ->get();
                foreach ($overdue as $b) {
                    $items[] = [
                        'kind' => 'overdue',
                        'title' => 'Menunggak: ' . ($b->student->name ?? 'siswa'),
                        'desc' => "{$b->bill_code} • " . ($b->feeCategory->name ?? '-') . ' • Rp ' . number_format($b->total_due, 0, ',', '.'),
                        'url' => route('admin.billing'),
                        'ts' => $b->updated_at?->timestamp ?? 0,
                    ];
                }

                $soon = Bill::with(['student', 'feeCategory'])
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '>=', $today)
                    ->whereDate('due_date', '<=', $today->copy()->addDays(3))
                    ->orderBy('due_date')
                    ->take(5)
                    ->get();
                foreach ($soon as $b) {
                    $daysLeft = (int) $today->diffInDays(Carbon::parse($b->due_date->format('Y-m-d')), false);
                    $when = $daysLeft <= 0 ? 'hari ini' : "H-{$daysLeft}";
                    $items[] = [
                        'kind' => 'reminder',
                        'title' => 'Jatuh tempo ' . $when . ': ' . ($b->student->name ?? 'siswa'),
                        'desc' => "{$b->bill_code} • Rp " . number_format($b->total_due, 0, ',', '.'),
                        'url' => route('admin.billing'),
                        'ts' => $b->due_date?->timestamp ?? 0,
                    ];
                }

                // Event overdue terbaru sebagai pelengkap.
                $events = BillEvent::with('bill.student')
                    ->where('event', 'overdue')
                    ->latest()
                    ->take(3)
                    ->get();
                foreach ($events as $ev) {
                    $items[] = [
                        'kind' => 'event',
                        'title' => 'Denda: ' . ($ev->bill->bill_code ?? '-'),
                        'desc' => $ev->message ?? 'Tagihan menunggak',
                        'url' => route('admin.billing'),
                        'ts' => $ev->created_at?->timestamp ?? 0,
                    ];
                }
            }

            usort($items, fn ($a, $b) => $b['ts'] <=> $a['ts']);
            $items = array_slice($items, 0, 8);

            return self::$memo = ['count' => count($items), 'items' => $items];
        } catch (\Throwable $e) {
            return self::$memo = ['count' => 0, 'items' => []];
        }
    }
}
