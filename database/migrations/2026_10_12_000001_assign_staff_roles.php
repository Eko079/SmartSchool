<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     Akun admin@smartschool.id -> superadmin.
     Akun bendahara@smartschool.id sudah 'admin' ( = Bendahara), tidak diubah.
    */
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'admin@smartschool.id')
            ->where('role', 'admin')
            ->update(['role' => 'superadmin']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@smartschool.id')
            ->where('role', 'superadmin')
            ->update(['role' => 'admin']);
    }
};
