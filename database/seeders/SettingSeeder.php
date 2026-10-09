<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'school_name' => 'SMA JAYA',
            'npsn' => '20104589',
            'school_email' => 'info@smkn1smartschool.sch.id',
            'school_phone' => '(021) 7812-9900',
            'school_address' => 'Jl. Pendidikan No. 45, Kebayoran Baru, Jakarta Selatan',
            'academic_year' => '2026/2027',
            'active_semester' => 'Ganjil',
            'gateway_bca_va' => '1',
            'gateway_bni_va' => '1',
            'gateway_qris' => '1',
            'midtrans_server_key' => 'SB-Mid-server-DemoSmartSchool2026',
            'midtrans_client_key' => 'SB-Mid-client-DemoSmartSchool2026',
            'midtrans_sandbox' => '1',
        ];

        foreach ($settings as $k => $v) {
            SchoolSetting::set($k, $v);
        }
    }
}

