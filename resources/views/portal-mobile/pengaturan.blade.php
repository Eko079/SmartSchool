@extends('layouts.portal-mobile')
@section('title', 'Pengaturan')

@section('hero')
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Pengaturan Akun</div></div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ $user->notify_wa ? 'Aktif' : 'Mati' }}</div>
        <div class="m-kpi-lab">Notifikasi WA</div>
        <div class="m-kpi-sub">Jatuh tempo</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ $user->notify_email ? 'Aktif' : 'Mati' }}</div>
        <div class="m-kpi-lab">Email</div>
        <div class="m-kpi-sub">Bukti bayar</div>
    </div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Profil Wali</div></div>
        <div class="m-kv"><span>Nama Wali</span><b>{{ $user->name }}</b></div>
        <div class="m-kv"><span>Telepon / WA</span><b>{{ $user->phone ?: ($user->student->guardian_phone ?? '-') }}</b></div>
        <div class="m-kv"><span>Siswa</span><b>{{ $user->student->name ?? '-' }}</b></div>
        <div class="m-fdesc">Data induk dari sekolah, untuk perubahan hanya via bendahara.</div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Notifikasi</div></div>
        <form method="POST" action="{{ route('portal.pengaturan.update') }}">
            @csrf
            @method('PUT')
            <label class="m-stu-sub" style="display:block;margin-bottom:8px"><input type="checkbox" name="notify_wa" value="1" {{ $user->notify_wa ? 'checked' : '' }}> Pengingat WA jatuh tempo</label>
            <label class="m-stu-sub" style="display:block"><input type="checkbox" name="notify_email" value="1" {{ $user->notify_email ? 'checked' : '' }}> Bukti bayar via email</label>
            <div class="m-actions" style="margin-top:10px">
                <button type="submit" class="m-btn m-btn-primary">Simpan Notifikasi</button>
            </div>
        </form>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Ganti Sandi</div></div>
        <form method="POST" action="{{ route('portal.pengaturan.sandi') }}">
            @csrf
            @method('PUT')
            <label class="m-label">Sandi Lama</label>
            <input type="password" name="current_password" class="m-input" required>
            <label class="m-label" style="margin-top:8px">Sandi Baru</label>
            <input type="password" name="password" class="m-input" required>
            <label class="m-label" style="margin-top:8px">Ulangi Sandi Baru</label>
            <input type="password" name="password_confirmation" class="m-input" required>
            <div class="m-actions" style="margin-top:10px">
                <button type="submit" class="m-btn m-btn-primary">Perbarui Sandi</button>
            </div>
        </form>
    </div>
@endsection
