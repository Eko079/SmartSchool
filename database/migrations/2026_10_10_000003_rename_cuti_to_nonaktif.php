<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif','cuti','nonaktif','lulus') NOT NULL DEFAULT 'aktif'");
        }

        DB::table('students')->where('status', 'cuti')->update(['status' => 'nonaktif']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif','nonaktif','lulus') NOT NULL DEFAULT 'aktif'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif','cuti','nonaktif','lulus') NOT NULL DEFAULT 'aktif'");
        }

        DB::table('students')->where('status', 'nonaktif')->update(['status' => 'cuti']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('aktif','cuti','lulus') NOT NULL DEFAULT 'aktif'");
        }
    }
};
