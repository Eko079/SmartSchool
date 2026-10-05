<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway', 30)->nullable()->after('payment_method');
            $table->string('transaction_id', 80)->nullable()->after('gateway');
            $table->string('va_number', 40)->nullable()->after('transaction_id');
            $table->timestamp('expiry_at')->nullable()->after('va_number');
            $table->timestamp('settlement_at')->nullable()->after('expiry_at');
            $table->json('callback_payload')->nullable()->after('settlement_at');
            $table->string('receipt_path')->nullable()->after('callback_payload');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'transaction_id', 'va_number', 'expiry_at', 'settlement_at', 'callback_payload', 'receipt_path']);
        });
    }
};
