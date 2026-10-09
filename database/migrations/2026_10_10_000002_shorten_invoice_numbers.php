<?php

use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $payments = DB::table('payments')
            ->join('bills', 'bills.id', '=', 'payments.bill_id')
            ->join('fee_categories', 'fee_categories.id', '=', 'bills.fee_category_id')
            ->select('payments.id', 'payments.invoice_number', 'fee_categories.code')
            ->where('payments.invoice_number', 'not like', 'KW-%')
            ->orderBy('payments.id')
            ->get();

        $seq = 0;
        foreach ($payments as $row) {
            $seq++;
            $code = strtoupper(trim($row->code));
            if (str_starts_with($code, 'PSAJ')) {
                $prefix = 'PSJ';
            } else {
                $alnum = preg_replace('/[^A-Z0-9]/', '', $code);
                $prefix = substr(str_pad($alnum, 3, 'X'), 0, 3);
            }
            $candidate = sprintf('KW-%s-%04d', $prefix, $seq);
            while (DB::table('payments')->where('invoice_number', $candidate)->exists()) {
                $seq++;
                $candidate = sprintf('KW-%s-%04d', $prefix, $seq);
            }
            DB::table('payments')->where('id', $row->id)->update(['invoice_number' => $candidate]);
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan ke format panjang — nomor kuitansi bersifat permanen.
    }
};
