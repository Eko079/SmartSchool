@extends('layouts.portal-mobile')
@section('title', 'Riwayat Pembayaran')

@section('hero')
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Riwayat Pembayaran</div></div>
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
        <input type="text" placeholder="Cari tagihan, kategori…" data-m-filter=".m-stu-card" aria-label="Cari riwayat">
    </div>

    <div class="m-sec-head"><div class="m-sec-title">Riwayat Pembayaran</div><a class="m-see" href="{{ request()->fullUrlWithQuery(['show' => request('show') === 'all' ? null : 'all']) }}">{{ request('show') === 'all' ? '‹ Tutup' : 'Detail ›' }}</a></div>
    @forelse((request('show') === 'all' ? $payments->getCollection() : $payments->getCollection()->take(3)) as $pay)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $pay->bill->display_name }}</div>
                    <div class="m-stu-sub">{{ $pay->invoice_number }} • {{ $pay->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                    <div class="m-stu-sub">{{ $pay->method_label }} • Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                </div>
                <div class="m-stu-price">Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                @if($pay->status === 'success')<span class="m-pill m-pill-ok">{{ $pay->status_label }}</span>
                @elseif($pay->status === 'pending')<span class="m-pill m-pill-warn">{{ $pay->status_label }}</span>
                @else<span class="m-pill m-pill-err">{{ $pay->status_label }}</span>@endif
            </div>
            <div class="m-actions">
                <a class="m-btn m-btn-primary" href="{{ route('portal.kuitansi', $pay->id) }}">Kuitansi</a>
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada riwayat pembayaran.</div></div>
    @endforelse

    @if($payments->total() > 3)
    <div class="m-actions" style="margin-top:10px">
        <a class="m-btn m-btn-primary" style="flex:1" href="{{ request()->fullUrlWithQuery(['show' => request('show') === 'all' ? null : 'all']) }}">{{ request('show') === 'all' ? 'Tutup' : 'Detail Riwayat Pembayaran' }}</a>
    </div>
    @endif

@endsection
