@extends('layouts.portal-auth')
@section('title', 'Login Portal')

@section('content')
<div class="text-xs font-bold tracking-wider text-blue-600">SISWA & ORANG TUA</div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Selamat Datang Kembali</h2>
<p class="text-sm text-slate-500">Masuk untuk lihat tagihan & riwayat pembayaran ananda.</p>

@if(session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-300">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('portal.login.attempt') }}" class="space-y-4 mt-2">
    @csrf
    <div>
        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">NIS / Email</label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input type="text" name="identifier" value="{{ old('identifier') }}" class="ss-input !pl-11" placeholder="cth: 2023071002 / wali@email.id" required autofocus>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Kata Sandi</label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <input type="password" name="password" id="password" class="ss-input !pl-11 !pr-11" placeholder="••••••••••" required>
            <button type="button" onclick="document.getElementById('password').type = document.getElementById('password').type === 'password' ? 'text' : 'password'" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-slate-700 dark:text-slate-300">Ingat saya</span>
        </label>
        <button type="button" onclick="document.getElementById('otp-panel').classList.toggle('hidden')" class="font-semibold text-blue-600 hover:underline">Lupa kata sandi?</button>
    </div>

    <button type="submit" class="ss-btn-primary">Masuk ke Portal
        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>
</form>

<div id="otp-panel" class="hidden ss-card space-y-3">
    <div>
        <h3 class="text-sm font-bold text-slate-800 dark:text-white">Lupa Kata Sandi</h3>
        <p class="text-xs text-slate-500">Masuk email wali, terima OTP 6 digit, buat sandi baru.</p>
    </div>
    <form method="POST" action="{{ route('portal.otp') }}" class="space-y-2">
        @csrf
        <input type="text" name="identifier" class="ss-input" placeholder="Email / No. WA wali" required>
        <button type="submit" class="ss-btn-outline !w-full">Kirim OTP</button>
    </form>
    <form method="POST" action="{{ route('portal.reset') }}" class="space-y-2 border-t border-slate-200 dark:border-slate-700 pt-3">
        @csrf
        <input type="text" name="identifier" class="ss-input" placeholder="Email / No. WA wali" required>
        <input type="text" name="code" class="ss-input" placeholder="OTP 6 digit" maxlength="6" required>
        <input type="password" name="password" class="ss-input" placeholder="Sandi baru min. 8 karakter" required>
        <input type="password" name="password_confirmation" class="ss-input" placeholder="Ulangi sandi baru" required>
        <button type="submit" class="ss-btn-primary !w-full">Simpan Sandi Baru</button>
    </form>
    <p class="text-[11px] text-slate-400">OTP berlaku 5 menit • salah 5x kunci 15 menit.</p>
</div>

<p class="text-center text-xs text-slate-400 flex items-center justify-center gap-2">
    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    <span>Gagal 5x akun dikunci 15 menit. Belum punya akses? Hubungi Operator.</span>
</p>
@endsection
