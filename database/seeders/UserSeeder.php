<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smartschool.id'],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'password' => Hash::make('admin123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'bendahara@smartschool.id'],
            [
                'name' => 'Siti Rahmawati, S.E',
                'password' => Hash::make('bendahara123'),
            ]
        );
    }
}

