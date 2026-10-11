@extends('layouts.admin')
@section('title', 'Akun Staf')
@section('page-title', 'Akun Staf')
@section('page-subtitle')
    Kelola akun pengelola: {{ $isSuper ? 'Super Admin dan Bendahara.' : 'Bendahara (sesama).' }}
@endsection

@section('content')
    <div class="ss-card space-y-4 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Akun</h2>
                <p class="text-xs text-slate-400">{{ $staff->count() }} akun • akun nonaktif tidak bisa masuk</p>
            </div>
            <button type="button" title="Tambah akun {{ $isSuper ? 'staf' : 'bendahara' }}"
                onclick="document.getElementById('staff-create-modal').classList.remove('hidden')"
                class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah {{ $isSuper ? 'Akun' : 'Bendahara' }}
            </button>
        </div>

        <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="py-2.5 px-3">Nama</th>
                        <th class="py-2.5 px-3">Email / No. WA</th>
                        <th class="py-2.5 px-3">Peran</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $s)
                    <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $s->name }}</td>
                        <td class="py-3 px-3">
                            <div class="text-slate-800 dark:text-slate-200">{{ $s->email }}</div>
                            <div class="text-[11px] text-slate-400">{{ $s->phone ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $s->role === 'superadmin' ? 'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-300' : 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' }}">{{ $s->role_label }}</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('admin.akun.reset', $s->id) }}" method="POST" class="inline"
                                    onsubmit="return ssConfirmSubmit(this, 'Reset sandi {{ addslashes($s->name) }}? Sandi baru ditampilkan sekali.', 'Reset')">
                                    @csrf
                                    <button type="submit" title="Reset sandi {{ $s->name }}"
                                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Reset Sandi</button>
                                </form>
                                @if($isSuper)
                                <form action="{{ route('admin.akun.toggle', $s->id) }}" method="POST" class="inline"
                                    onsubmit="return ssConfirmSubmit(this, '{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ addslashes($s->name) }}?', '{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}')">
                                    @csrf
                                    <button type="submit" title="{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="rounded-md border {{ $s->is_active ? 'border-rose-200 dark:border-rose-900 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950' : 'border-emerald-200 dark:border-emerald-900 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950' }} px-2 py-1 text-[11px] font-semibold transition">{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada akun staf.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Akun --}}
    <div id="staff-create-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah {{ $isSuper ? 'Akun Staf' : 'Bendahara' }}</h3>
                <button onclick="document.getElementById('staff-create-modal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.akun.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" class="ss-input text-xs !py-2" placeholder="Nama pengelola" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" class="ss-input text-xs !py-2" placeholder="email@sekolah.id" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WA</label>
                        <input type="text" name="phone" class="ss-input text-xs !py-2" placeholder="08...">
                    </div>
                    @if($isSuper)
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran <span class="text-rose-500">*</span></label>
                        <select name="role" class="ss-input text-xs !py-2">
                            <option value="admin">Bendahara</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    @endif
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                    <input type="text" name="password" minlength="8" class="ss-input text-xs !py-2" placeholder="Min. 8 karakter" required>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="document.getElementById('staff-create-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
