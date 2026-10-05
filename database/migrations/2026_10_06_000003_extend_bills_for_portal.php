<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('order_id', 60)->nullable()->unique()->after('bill_code');
            $table->decimal('fine_amount', 14, 2)->default(0)->after('paid_amount');
            $table->string('va_number', 40)->nullable()->after('fine_amount');
            $table->text('qris_payload')->nullable()->after('va_number');
            $table->timestamp('expired_at')->nullable()->after('qris_payload');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'fine_amount', 'va_number', 'qris_payload', 'expired_at']);
        });
    }
};
