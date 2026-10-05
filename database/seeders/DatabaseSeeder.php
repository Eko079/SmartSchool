<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolClassSeeder::class,
            StudentSeeder::class,
            BillingSeeder::class,
            SettingSeeder::class,
            PortalSeeder::class,
        ]);
    }
}

