@extends('layouts.portal')
@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="ss-card space-y-3 flex flex-col">
            <div>
                <h2 class="text-sm font-bold">Profil Wali</h2>
                <p class="text-xs text-slate-400">Data induk dari sekolah — perubahan hanya via bendahara</p>
            </div>
            <dl class="grid grid-cols-1 gap-2 text-xs flex-1">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Wali</dt>
                    <dd class="mt-1 font-semibold">{{ $user->name }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Telepon / WA</dt>
                    <dd class="mt-1 font-semibold">{{ $user->phone ?: ($user->student->guardian_phone ?? '-') }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Siswa</dt>
                    <dd class="mt-1 font-semibold">{{ $user->student->name ?? '-' }}{{ $user->student?->nis ? ' • NIS ' . $user->student->nis : '' }}</dd>
                </div>
            </dl>
            <p class="text-[11px] text-slate-400">Ada yang salah? Hubungi bendahara sekolah.</p>
            <form method="POST" action="{{ route('portal.pengaturan.update') }}" class="flex flex-col gap-3">
                @csrf
                @method('PUT')
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="checkbox" name="notify_wa" value="1" {{ old('notify_wa', $user->notify_wa) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-blue-600">
                    Pengingat jatuh tempo via WA
                </label>
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="checkbox" name="notify_email" value="1" {{ old('notify_email', $user->notify_email) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-blue-600">
                    Kirim bukti bayar via email
                </label>
                <button type="submit" class="ss-btn-primary py-2.5! text-sm! mt-auto">Simpan Notifikasi</button>
            </form>
        </div>

        <div class="ss-card space-y-3 flex flex-col">
            <div>
                <h2 class="text-sm font-bold">Sandi & Keamanan</h2>
                <ul class="text-xs text-slate-400 space-y-0.5">
                    <li>Min. 8 karakter</li>
                    <li>Gagal 5x kunci 15 menit</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('portal.pengaturan.sandi') }}" class="flex flex-col gap-3 flex-1">
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
