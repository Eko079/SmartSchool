<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->year('entry_year')->nullable()->after('email');
            $table->string('photo_path')->nullable()->after('entry_year');
            $table->date('birthdate')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['email', 'entry_year', 'photo_path', 'birthdate']);
        });
    }
};
