@extends('layouts.mobile')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Lainnya / Pengaturan')

@section('hero')
    <div class="m-rev-label">PENGATURAN</div>
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">{{ $schoolName }}</div></div>
    <div class="m-rev-sub">TA {{ $settings['academic_year'] ?? '-' }} · Semester {{ $settings['active_semester'] ?? '-' }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ $settings['academic_year'] ?? '-' }}</div>
        <div class="m-kpi-lab">Tahun Ajaran</div>
        <div class="m-kpi-sub">Aktif</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ $settings['active_semester'] ?? '-' }}</div>
        <div class="m-kpi-lab">Semester</div>
        <div class="m-kpi-sub">Berjalan</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ ($settings['midtrans_sandbox'] ?? '0') === '1' ? 'Uji' : 'Live' }}</div>
        <div class="m-kpi-lab">Gateway</div>
        <div class="m-kpi-sub">Mode</div>
    </div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Profil Sekolah</div></div>
        <div class="m-stu-sub">Nama: {{ $settings['school_name'] ?? '-' }}</div>
        <div class="m-stu-sub">NPSN: {{ $settings['npsn'] ?? '-' }}</div>
        <div class="m-stu-sub">Email: {{ $settings['school_email'] ?? '-' }}</div>
        <div class="m-stu-sub">Telepon: {{ $settings['school_phone'] ?? '-' }}</div>
        <div class="m-stu-sub">Alamat: {{ $settings['school_address'] ?? '-' }}</div>
        <div class="m-actions"><a class="m-btn" href="{{ route('admin.pengaturan') }}">Ubah di versi desktop</a></div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Pembayaran</div></div>
        <div class="m-stu-sub">BCA VA: {{ ($settings['gateway_bca_va'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
        <div class="m-stu-sub">BNI VA: {{ ($settings['gateway_bni_va'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
        <div class="m-stu-sub">QRIS: {{ ($settings['gateway_qris'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Notifikasi</div></div>
        <div class="m-stu-sub">Ubah pengaturan lewat desktop agar validasi penuh.</div>
        <div class="m-actions"><a class="m-btn m-btn-primary" href="{{ route('admin.pengaturan') }}">Buka Pengaturan</a></div>
    </div>
@endsection
