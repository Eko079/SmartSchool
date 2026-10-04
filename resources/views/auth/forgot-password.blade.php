@extends('layouts.auth')
@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="text-xs font-bold tracking-wider text-blue-600">ADMINISTRATOR</div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Lupa Kata Sandi</h2>
<p class="text-sm text-slate-500">Masukkan email sekolah, terima OTP 6 digit via email, buat sandi baru.</p>

<form method="POST" action="#" class="space-y-4 mt-2">
    @csrf
    <div>
        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Email Sekolah Terdaftar</label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
            </span>
            <input type="email" name="email" class="ss-input !pl-11" placeholder="admin@smanusantara.sch.id" required autofocus>
        </div>
    </div>

    {{-- OTP section --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-4 space-y-3">
        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Langkah 2 · Masukkan OTP 6 digit</div>
        <div class="flex justify-center gap-2">
            @for($i = 0; $i < 6; $i++)
            <input type="text" maxlength="1" class="w-11 h-13 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-center text-xl font-bold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100" name="otp[]">
            @endfor
        </div>
        <p class="text-xs text-slate-500">Berlaku 5 menit · salah 5x kunci 15 menit · <button type="button" class="text-blue-600 font-semibold">Kirim ulang</button></p>
    </div>

    {{-- New password --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-4 space-y-3">
        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Langkah 3 · Sandi baru min. 8 karakter</div>
        <input type="password" name="password" class="ss-input text-sm" placeholder="Sandi baru">
        <input type="password" name="password_confirmation" class="ss-input text-sm" placeholder="Ulangi sandi baru">
    </div>

    <div class="text-center">
        <a href="{{ url('/login') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Kembali ke Login</a>
    </div>

    <button type="submit" class="ss-btn-primary">Kirim OTP →</button>
</form>

<p class="text-center text-xs text-slate-400 flex items-center justify-center gap-2">
    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    Gagal 5x akun dikunci 15 menit • Belum punya akses? Hubungi Operator
</p>
@endsection
