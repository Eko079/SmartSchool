<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->string('semester', 10)->nullable()->after('type');
            $table->json('majors')->nullable()->after('semester');
            $table->json('grades')->nullable()->after('majors');
            $table->string('audience', 10)->nullable()->after('grades');
            $table->unsignedTinyInteger('due_month')->nullable()->after('audience');
            $table->unsignedTinyInteger('due_day')->nullable()->after('due_month');
            $table->unsignedInteger('sort_order')->default(500)->after('due_day');
        });
    }

    public function down(): void
    {
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->dropColumn(['semester', 'majors', 'grades', 'audience', 'due_month', 'due_day', 'sort_order']);
        });
    }
};
