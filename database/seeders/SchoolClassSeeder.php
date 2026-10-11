<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [];
        foreach (['X', 'XI', 'XII'] as $grade) {
            foreach (['IPA' => 3, 'IPS' => 3, 'IBB' => 2] as $major => $rombels) {
                for ($i = 1; $i <= $rombels; $i++) {
                    $classes[] = [
                        'name' => "{$grade} {$major} {$i}",
                        'grade' => $grade,
                        'major' => $major,
                        'capacity' => 36,
                    ];
                }
            }
        }

        foreach ($classes as $c) {
            ClassRoom::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
