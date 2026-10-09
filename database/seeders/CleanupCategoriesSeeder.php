<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class CleanupCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus kategori TEST-* yang tidak punya tagihan.
        $testCats = FeeCategory::where('code', 'like', 'TEST-%')->get();
        foreach ($testCats as $cat) {
            if (Bill::where('fee_category_id', $cat->id)->count() === 0) {
                $cat->delete();
            } else {
                $cat->update(['is_active' => false]);
            }
        }

        // Rename SPP-2026 -> SPP (lintas tahun ajaran).
        $spp = FeeCategory::where('code', 'SPP-2026')->first();
        if ($spp) {
            $existing = FeeCategory::where('code', 'SPP')->first();
            if ($existing && $existing->id !== $spp->id) {
                Bill::where('fee_category_id', $spp->id)->update(['fee_category_id' => $existing->id]);
                $spp->delete();
            } else {
                $spp->update(['code' => 'SPP', 'name' => 'SPP Bulanan']);
            }
        }

        // Nonaktifkan duplikat lama yang digantikan paket semester.
        FeeCategory::whereIn('code', ['LAB-01', 'KEG-01'])->update(['is_active' => false]);
    }
}
