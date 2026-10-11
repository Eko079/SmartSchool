@extends('layouts.admin')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Data akun Anda yang sedang login.')
@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="ss-card space-y-3">
            <div>
                <h2 class="text-sm font-bold">Data Akun</h2>
                <p class="text-xs text-slate-400">Perubahan peran hanya via Super Admin</p>
            </div>
            <dl class="grid grid-cols-1 gap-2 text-xs">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama</dt>
                    <dd class="mt-1 font-semibold">{{ $user->name }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email Login</dt>
                    <dd class="mt-1 font-semibold break-all">{{ $user->email }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Peran</dt>
                    <dd class="mt-1 font-semibold">{{ $user->role_label }}</dd>
                </div>
            </dl>
        </div>

        <div class="ss-card space-y-3 flex flex-col">
            <div>
                <h2 class="text-sm font-bold">Sandi & Keamanan</h2>
                <p class="text-xs text-slate-400">Min. 8 karakter</p>
            </div>
            <form method="POST" action="{{ route('admin.profil.sandi') }}" class="flex flex-col gap-3 flex-1">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Kata Sandi Lama</label>
                    <input type="password" name="current_password" class="ss-input" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Kata Sandi Baru</label>
                    <input type="password" name="password" class="ss-input" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Ulangi Sandi Baru</label>
                    <input type="password" name="password_confirmation" class="ss-input" required>
                </div>
                <button type="submit" class="ss-btn-primary py-2.5! text-sm! mt-auto">Perbarui Sandi</button>
            </form>
        </div>
    </div>
@endsection
