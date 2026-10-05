@extends('layouts.portal-mobile')
@section('title', 'Riwayat Bayar')
@section('breadcrumb', 'Pembayaran / Riwayat')

@section('hero')
    <div class="m-rev-label">RIWAYAT • {{ $payments->total() }} TRANSAKSI</div>
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Lunas & Kuitansi</div></div>
    <div class="m-rev-sub">Unduh bukti tiap transaksi sukses</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $payments->total() }}" data-m-fmt="int">{{ number_format($payments->total(), 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Transaksi</div>
        <div class="m-kpi-sub">Tercatat</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">PDF</div>
        <div class="m-kpi-lab">Kuitansi</div>
        <div class="m-kpi-sub">Tersedia</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">VA</div>
        <div class="m-kpi-lab">QRIS</div>
        <div class="m-kpi-sub">Otomatis</div>
    </div>
@endsection

@section('content')
    <div class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari invoice, kategori…" data-m-filter=".m-stu-card" aria-label="Cari riwayat">
    </div>

    <div class="m-sec-head"><div class="m-sec-title">Tagihan per Bulan</div><span class="m-see">{{ $payments->total() }} transaksi</span></div>
    @forelse($payments as $pay)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $pay->invoice_number }} • {{ $pay->bill->feeCategory->name ?? '-' }}</div>
                    <div class="m-stu-sub">{{ $pay->paid_at?->format('d M Y H:i') ?? '-' }} • {{ $pay->payment_method }}</div>
                    <div class="m-stu-sub">Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                </div>
                @if($pay->status === 'success')<span class="m-pill m-pill-ok">Lunas</span>
                @elseif($pay->status === 'pending')<span class="m-pill m-pill-warn">Menunggu</span>
                @else<span class="m-pill m-pill-err">{{ ucfirst($pay->status) }}</span>@endif
            </div>
            <div class="m-actions">
                <a class="m-btn m-btn-primary" href="{{ route('portal.kuitansi', $pay->id) }}">Kuitansi</a>
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada riwayat pembayaran.</div></div>
    @endforelse

    @if($payments->hasPages())
        <div class="m-card"><div class="m-actions">
            @if($payments->previousPageUrl())<a class="m-btn" href="{{ $payments->previousPageUrl() }}">Kembali</a>@endif
            @if($payments->nextPageUrl())<a class="m-btn m-btn-primary" href="{{ $payments->nextPageUrl() }}">Lanjut</a>@endif
        </div></div>
    @endif

    <div class="m-card"><div class="m-actions">
        <a class="m-btn m-btn-primary" href="{{ route('portal.tagihan') }}">Bayar Tagihan</a>
    </div></div>
@endsection
