<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('academic_year', 9)->nullable()->after('period_year')->index();
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->after('academic_year')->index();
        });

        // Tambah tipe semesteran ke fee_categories.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fee_categories MODIFY COLUMN type ENUM('bulanan','sekali','bebas','semesteran') NOT NULL DEFAULT 'bulanan'");
        }
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'semester']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fee_categories MODIFY COLUMN type ENUM('bulanan','sekali','bebas') NOT NULL DEFAULT 'bulanan'");
        }
    }
};
