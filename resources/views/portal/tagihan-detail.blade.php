@extends('layouts.portal')
@section('title', 'Detail Tagihan')
@section('breadcrumb', 'Portal / Tagihan / ' . $bill->bill_code)
@section('page-title', $bill->bill_code)
@section('page-subtitle', ($bill->feeCategory->name ?? '-') . ' • jatuh tempo ' . ($bill->due_date?->format('d M Y') ?? '-'))

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <x-ss-pill status="{{ $bill->status }}" label="{{ ucfirst($bill->status) }}" />
            <span class="text-xs text-slate-400">Order {{ $bill->order_id ?? '-' }}</span>
        </div>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori</dt>
                <dd class="mt-1 font-semibold">{{ $bill->feeCategory->name ?? '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Periode</dt>
                <dd class="mt-1 font-semibold">{{ $bill->period_month }}/{{ $bill->period_year }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</dt>
                <dd class="mt-1 font-semibold">{{ $bill->academic_year ?? '-' }} • {{ $bill->semester ? ucfirst($bill->semester) : '-' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nominal</dt>
                <dd class="mt-1 font-semibold">{{ $rp($bill->amount) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Terbayar</dt>
                <dd class="mt-1 font-semibold">{{ $rp($bill->paid_amount) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Denda</dt>
                <dd class="mt-1 font-semibold">{{ $rp($bill->fine_amount) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jatuh Tempo</dt>
                <dd class="mt-1 font-semibold">{{ $bill->due_date?->format('d M Y') ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    @if($bill->va_number)
    <div class="rounded-2xl bg-[#0F1E33] p-5 text-white space-y-1">
        <div class="text-xs font-semibold text-blue-300">Virtual Account • berlaku hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }}</div>
        <div class="text-2xl font-bold tracking-wider">{{ $bill->va_number }}</div>
        <div class="text-[11px] text-blue-200">Salin nomor ini bila bayar manual • auto-callback Midtrans aktif</div>
    </div>
    @endif

    @if($bill->status !== 'paid')
    <div class="ss-card space-y-3">
        <h2 class="text-sm font-bold">Bayar Tagihan</h2>
        <form method="POST" action="{{ route('portal.tagihan.pay', $bill->id) }}" class="flex flex-wrap gap-2">
            @csrf
            <select name="method" class="ss-input !w-auto">
                <option value="bca_va">BCA Virtual Account</option>
                <option value="bni_va">BNI Virtual Account</option>
                <option value="qris">QRIS Dinamis</option>
            </select>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Buat Pembayaran</button>
        </form>
    </div>
    @endif

    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold">Timeline</h2>
        @forelse($bill->events as $ev)
        <div class="flex gap-2.5 text-xs">
            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
            <div>
                <div class="font-medium">{{ $ev->message ?? $ev->event }}</div>
                <div class="text-[11px] text-slate-400">{{ $ev->created_at?->format('d M Y H:i') }}</div>
            </div>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada event. Pembayaran dan notifikasi WA tercatat di sini.</p>
        @endforelse
    </div>

    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold">Pembayaran</h2>
        @forelse($bill->payments as $pay)
        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3 text-xs">
            <span class="font-bold">{{ $pay->invoice_number }}</span>
            <x-ss-pill status="{{ $pay->status }}" label="{{ ucfirst($pay->status) }}" />
            <span class="font-semibold">{{ $rp($pay->amount) }}</span>
            <a href="{{ route('portal.kuitansi', $pay->id) }}" class="text-blue-600 hover:underline font-semibold">Kuitansi</a>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada pembayaran.</p>
        @endforelse
    </div>
@endsection
