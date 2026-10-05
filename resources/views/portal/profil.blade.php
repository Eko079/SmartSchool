@extends('layouts.portal')
@section('title', 'Profil Anak')
@section('breadcrumb', 'Portal / Profil')
@section('page-title', 'Profil Anak')
@section('page-subtitle', $student->nis . ' • ' . ($student->classRoom->name ?? '-'))

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-bold text-white">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <div>
                <h2 class="text-sm font-bold">{{ $student->name }}</h2>
                <p class="text-xs text-slate-400">NIS {{ $student->nis }} • {{ $student->classRoom->name ?? '-' }} • Masuk {{ $student->entry_year ?? '-' }}</p>
            </div>
            <span class="ml-auto"><x-ss-pill status="{{ $student->status }}" label="{{ ucfirst($student->status) }}" /></span>
        </div>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NISN</dt>
                <dd class="mt-1 font-semibold">{{ $student->nisn ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</dt>
                <dd class="mt-1 font-semibold break-all">{{ $student->email ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">No. WA Wali</dt>
                <dd class="mt-1 font-semibold">{{ $student->guardian_phone ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="ss-card">
            <div class="text-xs text-slate-500">Total Invoice</div>
            <div class="text-xl font-bold">{{ $student->bills->count() }} • {{ $rp($student->bills->sum('amount')) }}</div>
        </div>
        <div class="ss-card">
            <div class="text-xs text-slate-500">Lunas</div>
            <div class="text-xl font-bold text-emerald-600">{{ $student->bills->where('status', 'paid')->count() }} • {{ $rp($student->bills->where('status', 'paid')->sum('amount')) }}</div>
        </div>
        <div class="ss-card">
            <div class="text-xs text-slate-500">Menunggu</div>
            <div class="text-xl font-bold text-amber-600">{{ $student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->count() }} • {{ $rp($student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('amount')) }}</div>
        </div>
    </div>

    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold">Riwayat Invoice • klik buka Detail Invoice</h2>
        @forelse($student->bills->sortByDesc('due_date')->take(8) as $bill)
        <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3 hover:border-blue-500 transition text-xs">
            <span class="flex-1 font-medium">{{ $bill->feeCategory->name ?? '-' }} • {{ $rp($bill->amount) }}</span>
            <x-ss-pill status="{{ $bill->status }}" label="{{ ucfirst($bill->status) }}" />
        </a>
        @empty
        <p class="text-xs text-slate-400">Belum ada invoice.</p>
        @endforelse
    </div>
@endsection
