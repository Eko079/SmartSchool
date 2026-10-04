@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="text-xs font-bold tracking-wider text-blue-600">ADMINISTRATOR</div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Selamat Datang Kembali</h2>
<p class="text-sm text-slate-500">Masuk ke panel admin untuk mengelola data sekolah Anda.</p>

@if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-300">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ url('/login') }}" class="space-y-4 mt-2">

    @csrf
    <div>
        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Email Sekolah</label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
            </span>
            <input type="email" name="email" class="ss-input !pl-11" placeholder="admin@smanusantara.sch.id" required autofocus>
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
        <a href="{{ url('/forgot-password') }}" class="font-semibold text-blue-600 hover:underline">Lupa kata sandi?</a>
    </div>

    <button type="submit" class="ss-btn-primary">Masuk ke SmartSchool →</button>
</form>

<div class="flex items-center gap-3">
    <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
    <span class="text-xs text-slate-400">atau</span>
    <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
</div>

<button class="ss-btn-outline">
    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
    Masuk dengan Google Workspace Sekolah
</button>

<p class="text-center text-xs text-slate-400 flex items-center justify-center gap-2">
    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    Gagal 5x akun dikunci 15 menit • Belum punya akses? Hubungi Operator
</p>
@endsection
