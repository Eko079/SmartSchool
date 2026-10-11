@extends('layouts.admin')
@section('title', 'Backup Data')
@section('page-title', 'Backup Data')
@section('page-subtitle', 'Unduh salinan database sebelum operasi besar. Hanya Super Admin.')
@section('content')
    <div class="ss-card max-w-lg space-y-4 shadow-xs">
        <div class="pb-3 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Salinan Database</h2>
            <p class="text-xs text-slate-400">Backup terakhir: {{ $last ?? 'belum pernah' }}</p>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            Unduh seluruh database (.sql) sebelum hapus data, generate paket, atau kenaikan kelas.
            Simpan file di tempat aman — restore dilakukan manual via phpMyAdmin/CLI.
        </p>
        <form method="POST" action="{{ route('admin.backup.download') }}"
            onsubmit="return ssConfirmSubmit(this, 'Unduh backup database sekarang?', 'Unduh')">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Backup Sekarang
            </button>
        </form>
    </div>
@endsection
