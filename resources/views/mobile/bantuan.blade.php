@extends('layouts.mobile')
@section('title', 'Bantuan')
@section('breadcrumb', 'Lainnya / Bantuan')

@section('hero')
    <div class="m-rev-label">PUSAT BANTUAN</div>
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Butuh bantuan?</div></div>
    <div class="m-rev-sub">Jawaban cepat tanpa hubungi CS</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val">Siswa</div>
        <div class="m-kpi-lab">Kelola NIS</div>
        <div class="m-kpi-sub">Data wali</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">Billing</div>
        <div class="m-kpi-lab">Generate</div>
        <div class="m-kpi-sub">Tagihan</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">Laporan</div>
        <div class="m-kpi-lab">Ekspor</div>
        <div class="m-kpi-sub">Rekap</div>
    </div>
@endsection

@section('content')
    <div class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari panduan, tagihan, laporan" data-m-filter=".m-faq" aria-label="Cari bantuan">
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Panduan Cepat</div></div>
        <div class="m-actions">
            <a class="m-btn" href="{{ route('admin.siswa') }}">Siswa</a>
            <a class="m-btn" href="{{ route('admin.billing') }}">Billing</a>
            <a class="m-btn" href="{{ route('admin.laporan') }}">Laporan</a>
        </div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">FAQ Populer</div></div>
        <div class="m-faq"><div class="m-stu-name">Cara tambah siswa baru?</div><div class="m-stu-sub">Hubungi admin untuk tambah via Data Siswa, atau minta akses desktop.</div></div>
        <div class="m-faq"><div class="m-stu-name">Tagihan gagal dibuat?</div><div class="m-stu-sub">Cek duplikat siswa + kategori + periode yang sama.</div></div>
        <div class="m-faq"><div class="m-stu-name">Laporan tidak muncul?</div><div class="m-stu-sub">Pastikan pembayaran berstatus sukses dan tanggal benar.</div></div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Kontak</div></div>
        <div class="m-stu-sub">Telepon: {{ $settings['school_phone'] ?? '-' }}</div>
        <div class="m-stu-sub">Email: {{ $settings['school_email'] ?? '-' }}</div>
        <div class="m-fdesc">Butuh aksi admin? Buka menu terkait di atas, semua tetap di tampilan mobile.</div>
    </div>
@endsection
