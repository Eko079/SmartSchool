<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('password');
            $table->foreignId('student_id')->nullable()->after('role')->constrained('students')->nullOnDelete();
            $table->string('google_id')->nullable()->unique()->after('student_id');
            $table->string('phone', 20)->nullable()->after('google_id');
            $table->unsignedTinyInteger('failed_attempts')->default(0)->after('phone');
            $table->timestamp('locked_until')->nullable()->after('failed_attempts');
            $table->boolean('notify_wa')->default(true)->after('locked_until');
            $table->boolean('notify_email')->default(true)->after('notify_wa');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn(['role', 'google_id', 'phone', 'failed_attempts', 'locked_until', 'notify_wa', 'notify_email']);
        });
    }
};
