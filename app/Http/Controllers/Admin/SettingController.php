<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Support\Device;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // HP => tampilan mobile otomatis (URL tetap /admin/pengaturan).
        if (Device::isPhone($request)) {
            return app(MobileController::class)->pengaturan();
        }

        $settings = SchoolSetting::pluck('value', 'key')->toArray();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.pengaturan', compact('settings', 'users'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:30',
            'school_email' => 'nullable|email|max:255',
            'school_phone' => 'nullable|string|max:30',
            'school_address' => 'nullable|string|max:500',
            'academic_year' => 'nullable|string|max:20',
            'active_semester' => 'nullable|in:Ganjil,Genap',
            'midtrans_server_key' => 'nullable|string|max:255',
            'midtrans_client_key' => 'nullable|string|max:255',
        ]);

        // Checkbox gateway/sandbox: kirim '1' bila dicentang, '0' bila tidak (anti nyangkut ON).
        foreach (['gateway_bca_va', 'gateway_bni_va', 'gateway_qris', 'midtrans_sandbox'] as $key) {
            SchoolSetting::set($key, $request->has($key) ? '1' : '0');
        }

        foreach ($validated as $key => $value) {
            SchoolSetting::set($key, $value ?? '');
        }

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan berhasil disimpan.');
    }
}

