<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\BillEvent;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

class WaService
{
    public function send(?int $userId, ?int $billId, string $destination, string $template, string $message = ''): NotificationLog
    {
        $log = NotificationLog::create([
            'user_id' => $userId ?? Auth::id(),
            'bill_id' => $billId,
            'channel' => 'wa',
            'destination' => $destination,
            'template' => $template,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        if ($billId) {
            BillEvent::create([
                'bill_id' => $billId,
                'event' => 'wa_sent',
                'message' => $message ?: ('WA terkirim ke ' . $destination . ' via template ' . $template),
            ]);
        }

        return $log;
    }
}
