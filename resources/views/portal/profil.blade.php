@extends('layouts.portal')
@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-bold text-white">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <h2 class="text-sm font-bold">{{ $student->name }}</h2>
            <span class="ml-auto"><x-ss-pill status="{{ $student->status }}" label="{{ ucfirst($student->status) }}" /></span>
        </div>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIS</dt>
                <dd class="mt-1 font-semibold">{{ $student->nis }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kelas</dt>
                <dd class="mt-1 font-semibold">{{ $student->classRoom->name ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Masuk</dt>
                <dd class="mt-1 font-semibold">{{ $student->entry_year ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NISN</dt>
                <dd class="mt-1 font-semibold">{{ $student->nisn ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</dt>
                <dd class="mt-1 font-semibold break-all">{{ $student->email ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">No. WA</dt>
                <dd class="mt-1 font-semibold">{{ $student->guardian_phone ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="ss-card p-3!">
            <div class="text-xs text-slate-500">Total Tagihan</div>
            <div class="text-xl font-bold text-blue-600">{{ $rp($student->bills->sum('amount')) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ $student->bills->count() }} Tagihan</div>
        </div>
        <div class="ss-card p-3!">
            <div class="text-xs text-slate-500">Lunas</div>
            <div class="text-xl font-bold text-emerald-600">{{ $rp($student->bills->where('status', 'paid')->sum('amount')) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ $student->bills->where('status', 'paid')->count() }} Tagihan Lunas</div>
        </div>
        <div class="ss-card p-3!">
            <div class="text-xs text-slate-500">Berjalan</div>
            <div class="text-xl font-bold text-amber-600">{{ $rp($student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('amount')) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ $student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->count() }} Tagihan Berjalan</div>
        </div>
    </div>

    <div class="ss-card space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold">Riwayat Pembayaran</h2>
        </div>
        @forelse($student->bills->sortByDesc('due_date')->take(8) as $bill)
        <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3 hover:border-blue-500 transition">
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $bill->feeCategory->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $bill->bill_code }}</div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ $rp($bill->amount) }}</div>
                <div class="text-[11px] text-slate-400 whitespace-nowrap">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }}</div>
            </div>
            <x-ss-pill status="{{ $bill->status }}" label="{{ $bill->status_label }}" />
        </a>
        @empty
        <p class="text-xs text-slate-400">Belum ada tagihan.</p>
        @endforelse
    </div>
@endsection
