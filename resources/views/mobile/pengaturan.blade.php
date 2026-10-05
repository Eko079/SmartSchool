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
        <div class="m-actions"><button type="button" class="m-btn" onclick="mOpenSettings()">Ubah di sini</button></div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Pembayaran</div></div>
        <div class="m-stu-sub">BCA VA: {{ ($settings['gateway_bca_va'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
        <div class="m-stu-sub">BNI VA: {{ ($settings['gateway_bni_va'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
        <div class="m-stu-sub">QRIS: {{ ($settings['gateway_qris'] ?? '0') === '1' ? 'Aktif' : 'Mati' }}</div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Notifikasi</div></div>
        <div class="m-stu-sub">Ubah pengaturan langsung dari popup mobile.</div>
        <div class="m-actions"><button type="button" class="m-btn m-btn-primary" onclick="mOpenSettings()">Buka Pengaturan</button></div>
    </div>

    {{-- Popup ubah pengaturan mobile — submit ke /m/pengaturan, tetap di /m --}}
    <div id="m-settings" class="m-modal hidden" role="dialog" aria-modal="true" aria-label="Ubah pengaturan">
        <div class="m-modal-card">
            <div class="m-modal-head">
                <div class="m-stu-mid"><div class="m-stu-name">Ubah Pengaturan</div><div class="m-stu-sub">Tersimpan ke database</div></div>
                <button type="button" class="m-modal-x" onclick="document.getElementById('m-settings').classList.add('hidden')" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('m.pengaturan.update') }}">
                @csrf
                <label class="m-label">Nama Sekolah</label>
                <input type="text" name="school_name" value="{{ $settings['school_name'] ?? '' }}" class="m-input">
                <div class="m-row2" style="margin-top:8px">
                    <div><label class="m-label">Tahun Ajaran</label><input type="text" name="academic_year" value="{{ $settings['academic_year'] ?? '' }}" class="m-input"></div>
                    <div><label class="m-label">Semester</label><select name="active_semester" class="m-select"><option value="Ganjil" {{ ($settings['active_semester'] ?? '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option><option value="Genap" {{ ($settings['active_semester'] ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option></select></div>
                </div>
                <label class="m-label" style="margin-top:8px">Telepon</label>
                <input type="text" name="school_phone" value="{{ $settings['school_phone'] ?? '' }}" class="m-input">
                <label class="m-label" style="margin-top:8px">Email</label>
                <input type="email" name="school_email" value="{{ $settings['school_email'] ?? '' }}" class="m-input">
                <div class="m-actions" style="margin-top:12px">
                    <button type="button" class="m-btn" onclick="document.getElementById('m-settings').classList.add('hidden')">Batal</button>
                    <button type="submit" class="m-btn m-btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function mOpenSettings() { document.getElementById('m-settings').classList.remove('hidden'); }
    document.getElementById('m-settings').addEventListener('click', function (e) { if (e.target === this) this.classList.add('hidden'); });
    </script>
@endsection
