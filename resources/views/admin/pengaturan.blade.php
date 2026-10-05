@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan / Umum')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Profil sekolah, tahun ajaran & pembayaran.')

@section('topbar-actions')
<button type="button" title="Ubah pengaturan" onclick="document.getElementById('settings-modal').classList.remove('hidden')"
    class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs"
    data-tip="Ubah pengaturan">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 3a2.8 2.8 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
    Ubah Pengaturan
</button>
@endsection

@section('content')
    {{-- Mode lihat: nilai dari $settings, edit via popup --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="space-y-4">
            <div class="ss-card space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Profil Sekolah</h2>
                    <p class="text-xs text-slate-400">Tampil di invoice dan portal siswa</p>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Sekolah</dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-white">{{ $settings['school_name'] ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NPSN / NSM</dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-white">{{ $settings['npsn'] ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email Resmi</dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-white break-all">{{ $settings['school_email'] ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Telepon</dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-white">{{ $settings['school_phone'] ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3 sm:col-span-2">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat Lengkap</dt>
                        <dd class="mt-1 font-semibold text-slate-800 dark:text-white">{{ $settings['school_address'] ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="ss-card space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Tahun Ajaran dan Semester</h2>
                    <p class="text-xs text-slate-400">Dipakai subtitle Data Siswa dan Laporan</p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</div>
                        <div class="mt-1 font-semibold text-slate-800 dark:text-white">{{ $settings['academic_year'] ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Semester Aktif</div>
                        <div class="mt-1"><x-ss-pill status="{{ strtolower($settings['active_semester'] ?? 'ganjil') === 'genap' ? 'aktif' : 'pending' }}" label="{{ $settings['active_semester'] ?? 'Ganjil' }}" /></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="ss-card space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pembayaran dan Midtrans</h2>
                    <p class="text-xs text-slate-400">Channel aktif tampil di portal wali/siswa</p>
                </div>
                @php
                    $gatewayOn = fn($k) => (string) ($settings[$k] ?? '1') === '1';
                    $gateways = [
                        'BCA Virtual Account' => $gatewayOn('gateway_bca_va'),
                        'BNI Virtual Account' => $gatewayOn('gateway_bni_va'),
                        'QRIS Dinamis' => $gatewayOn('gateway_qris'),
                    ];
                    $mask = fn($v) => $v ? substr($v, 0, 8) . str_repeat('•', max(0, min(12, strlen($v) - 8))) : '-';
                @endphp
                <div class="space-y-2 text-xs">
                    @foreach($gateways as $label => $on)
                    <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                        <x-ss-pill status="{{ $on ? 'aktif' : 'nonaktif' }}" label="{{ $on ? 'Aktif' : 'Nonaktif' }}" />
                    </div>
                    @endforeach
                </div>
                <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-semibold text-slate-500">Server Key</span>
                        <code class="font-mono text-slate-700 dark:text-slate-200">{{ $mask($settings['midtrans_server_key'] ?? '') }}</code>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-semibold text-slate-500">Client Key</span>
                        <code class="font-mono text-slate-700 dark:text-slate-200">{{ $mask($settings['midtrans_client_key'] ?? '') }}</code>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-semibold text-slate-500">Mode Sandbox</span>
                        <x-ss-pill status="{{ $gatewayOn('midtrans_sandbox') ? 'pending' : 'aktif' }}" label="{{ $gatewayOn('midtrans_sandbox') ? 'Sandbox' : 'Produksi' }}" />
                    </div>
                </div>
            </div>

            <div class="ss-card space-y-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pengguna dan Keamanan</h2>
                    <p class="text-xs text-slate-400">Staf pengelola sistem</p>
                </div>
                <div class="space-y-2 text-xs">
                    @forelse($users ?? [] as $u)
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-bold">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                            <div>
                                <div class="font-bold text-slate-800 dark:text-white">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u->email }}</div>
                            </div>
                        </div>
                        <span class="rounded-md bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2 py-0.5">Admin</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Belum ada pengguna.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @include('admin.pengaturan.edit-modal')
@endsection
