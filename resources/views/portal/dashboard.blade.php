@extends('layouts.portal')
@section('title', 'Beranda')
@section('breadcrumb', 'Portal / Beranda')
@section('page-title', 'Beranda')
@section('page-subtitle', $student->name . ' • ' . ($student->classRoom->name ?? '-') . ' • ' . $stats['active_count'] . ' aktif, ' . $stats['paid_count'] . ' lunas')

@section('content')
    @php
        $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
        $cards = [
            ['title' => 'Menunggu Dibayar', 'val' => $rp($stats['waiting_amount']), 'count' => (float) $stats['waiting_amount'], 'fmt' => 'rp', 'bg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400', 'sub' => $stats['active_count'] . ' tagihan aktif'],
            ['title' => 'Sudah Lunas', 'val' => $stats['paid_count'] . ' tagihan', 'count' => (float) $stats['paid_count'], 'fmt' => 'int', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400', 'sub' => 'termasuk kuitansi tersedia'],
            ['title' => 'Tunggakan', 'val' => $stats['overdue_count'] . ' tagihan', 'count' => (float) $stats['overdue_count'], 'fmt' => 'int', 'bg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-400', 'sub' => $stats['nearest_due'] ? 'terdekat ' . $stats['nearest_due']->bill_code : 'tidak ada tunggakan'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($cards as $s)
        <div class="ss-card flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $s['title'] }}</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $s['bg'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 14l6-6M9 8h.01M15 14h.01M4 4h16v16H4z"/></svg>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ $s['count'] }}" data-count-fmt="{{ $s['fmt'] }}">{{ $s['val'] }}</div>
                <div class="text-xs text-slate-400 mt-1">{{ $s['sub'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="ss-card lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Tagihan Terbaru</h2>
                    <p class="text-xs text-slate-500">Klik untuk bayar</p>
                </div>
                <a href="{{ route('portal.tagihan') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-2">
                @forelse($latest_bills as $bill)
                <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3 hover:border-blue-500 transition">
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $bill->bill_code }} • {{ $bill->feeCategory->name ?? '-' }}</div>
                        <div class="text-[11px] text-slate-400">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }}</div>
                    </div>
                    <x-ss-pill status="{{ $bill->status }}" label="{{ ucfirst($bill->status) }}" />
                    <div class="text-xs font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ $rp($bill->amount) }}</div>
                </a>
                @empty
                <p class="text-xs text-slate-400">Belum ada tagihan.</p>
                @endforelse
            </div>
        </div>

        <div class="ss-card space-y-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Aktivitas Terbaru</h2>
            <div class="space-y-2.5">
                @forelse($activities as $ev)
                <div class="flex gap-2.5 text-xs">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                    <div>
                        <div class="font-medium text-slate-700 dark:text-slate-200">{{ $ev->message ?? $ev->event }}</div>
                        <div class="text-[11px] text-slate-400">{{ $ev->created_at?->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Belum ada aktivitas. Tagihan terbit dan pembayaran tercatat di sini.</p>
                @endforelse
            </div>
            @if($stats['nearest_due'])
            <div class="rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-900 p-3 text-xs">
                <div class="font-bold text-amber-800 dark:text-amber-200">Batas Pembayaran</div>
                <div class="text-amber-700 dark:text-amber-300">{{ $stats['nearest_due']->bill_code }} jatuh tempo {{ $stats['nearest_due']->due_date?->format('d M Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="ss-card space-y-3">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Tagihan per Semester</h2>
            <p class="text-xs text-slate-500">Ringkasan tahun ajaran aktif</p>
        </div>
        @if(($per_semester ?? collect())->isNotEmpty())
        <div class="space-y-2">
            @foreach($per_semester as $label => $row)
            <div class="flex items-center gap-3 text-xs">
                <span class="w-36 shrink-0 font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $label }}</span>
                <div class="flex-1 h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ max(4, round($row['total'] / max(1, $per_semester->max('total')) * 100)) }}%"></div>
                </div>
                <span class="w-24 shrink-0 text-right font-bold text-slate-800 dark:text-white">{{ $rp($row['total']) }}</span>
                <span class="w-16 shrink-0 text-right text-slate-400">{{ $row['count'] }} tagihan</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-slate-400">Belum ada data semester. Tagihan lama tanpa TA tampil di kategori.</p>
        @endif
    </div>

    <div class="ss-card space-y-3">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pembayaran per Kategori</h2>
            <p class="text-xs text-slate-500">Distribusi nominal tagihan ananda</p>
        </div>
        @if($per_category->isNotEmpty())
        <div class="space-y-2">
            @php $maxCat = max(1, $per_category->max()); @endphp
            @foreach($per_category as $name => $total)
            @php $pct = round($total / $per_category->sum() * 100, 1); @endphp
            <div class="flex items-center gap-3 text-xs">
                <span class="w-28 shrink-0 font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $name }}</span>
                <div class="flex-1 h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-600" style="width: {{ max(4, round($total / $maxCat * 100)) }}%"></div>
                </div>
                <span class="w-24 shrink-0 text-right font-bold text-slate-800 dark:text-white">{{ $rp($total) }}</span>
                <span class="w-12 shrink-0 text-right text-slate-400">{{ number_format($pct, 1, ',', '.') }}%</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-slate-400">Belum ada data kategori.</p>
        @endif
    </div>
@endsection
