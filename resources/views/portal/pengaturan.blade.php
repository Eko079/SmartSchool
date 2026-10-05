@extends('layouts.portal')
@section('title', 'Pengaturan Akun')
@section('breadcrumb', 'Portal / Pengaturan')
@section('page-title', 'Pengaturan Akun')
@section('page-subtitle', 'Profil, notifikasi WA & keamanan akun.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="ss-card space-y-3">
            <div>
                <h2 class="text-sm font-bold">Profil Wali</h2>
                <p class="text-xs text-slate-400">Data tampil di invoice & kuitansi</p>
            </div>
            <form method="POST" action="{{ route('portal.pengaturan.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Nama Wali</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="ss-input" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="ss-input" placeholder="08xx-xxxx-xxxx">
                </div>
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="checkbox" name="notify_wa" value="1" {{ old('notify_wa', $user->notify_wa) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-blue-600">
                    Pengingat jatuh tempo via WA
                </label>
                <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input type="checkbox" name="notify_email" value="1" {{ old('notify_email', $user->notify_email) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-blue-600">
                    Kirim bukti bayar via email
                </label>
                <button type="submit" class="ss-btn-primary !py-2.5 !text-sm">Simpan Perubahan</button>
            </form>
        </div>

        <div class="ss-card space-y-3">
            <div>
                <h2 class="text-sm font-bold">Sandi & Keamanan</h2>
                <p class="text-xs text-slate-400">Min. 8 karakter • gagal 5x kunci 15 menit</p>
            </div>
            <form method="POST" action="{{ route('portal.pengaturan.sandi') }}" class="space-y-3">
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
                <button type="submit" class="ss-btn-outline !w-full !py-2.5 !text-sm">Perbarui Sandi</button>
            </form>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3 text-xs space-y-1">
                <div class="font-semibold">Anak tertaut</div>
                <div>{{ $user->student->name ?? '-' }} • NIS {{ $user->student->nis ?? '-' }}</div>
                <div class="text-slate-400">{{ $user->student->classRoom->name ?? '-' }} • {{ $user->email }}</div>
            </div>
        </div>
    </div>
@endsection
