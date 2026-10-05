@extends('layouts.portal')
@section('title', 'Riwayat Bayar')
@section('breadcrumb', 'Portal / Riwayat')
@section('page-title', 'Riwayat Pembayaran')
@section('page-subtitle', 'Transaksi lunas • unduh kuitansi.')

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-2">
        @forelse($payments as $pay)
        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3">
            <div class="flex-1 min-w-[200px]">
                <div class="text-xs font-bold">{{ $pay->invoice_number }} • {{ $pay->bill->feeCategory->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $pay->paid_at?->format('d M Y H:i') ?? '-' }} • {{ $pay->payment_method }}</div>
            </div>
            <x-ss-pill status="{{ $pay->status }}" label="{{ ucfirst($pay->status) }}" />
            <span class="text-xs font-bold">{{ $rp($pay->amount) }}</span>
            <a href="{{ route('portal.kuitansi', $pay->id) }}" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-blue-500 hover:text-blue-600">Kuitansi</a>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada riwayat pembayaran.</p>
        @endforelse

        <div class="pt-2">{{ $payments->links() }}</div>
    </div>
@endsection
