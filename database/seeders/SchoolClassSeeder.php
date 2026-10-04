<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'X RPL 1', 'grade' => 'X', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'X RPL 2', 'grade' => 'X', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'X TKJ 1', 'grade' => 'X', 'major' => 'Teknik Komputer Jaringan'],
            ['name' => 'XI RPL 1', 'grade' => 'XI', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'XI RPL 2', 'grade' => 'XI', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'XI TKJ 1', 'grade' => 'XI', 'major' => 'Teknik Komputer Jaringan'],
            ['name' => 'XII RPL 1', 'grade' => 'XII', 'major' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'XII MM 1', 'grade' => 'XII', 'major' => 'Multimedia / DKV'],
        ];

        foreach ($classes as $c) {
            ClassRoom::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}

