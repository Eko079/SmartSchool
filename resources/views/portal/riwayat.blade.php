@extends('layouts.portal')
@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold">Riwayat Pembayaran</h2>
        </div>
        @forelse($payments as $pay)
        <div class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3">
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold truncate">{{ $pay->bill->feeCategory->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $pay->invoice_number }}</div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs font-bold whitespace-nowrap">{{ $rp($pay->amount) }}</div>
                <div class="text-[11px] text-slate-400 whitespace-nowrap">{{ $pay->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                <div class="text-[11px] text-slate-400 whitespace-nowrap">{{ $pay->method_label }}</div>
            </div>
            <x-ss-pill status="{{ $pay->status }}" label="{{ $pay->status_label }}" />
            <a href="{{ route('portal.kuitansi', $pay->id) }}" target="_blank" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-blue-500 hover:text-blue-600 shrink-0">Cetak</a>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada riwayat pembayaran.</p>
        @endforelse

        <div class="pt-2">{{ $payments->links() }}</div>
    </div>
@endsection
