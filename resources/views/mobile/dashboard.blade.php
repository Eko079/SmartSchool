@extends('layouts.mobile')
@section('title', 'Beranda')
@section('breadcrumb', 'Beranda / Dashboard')

@php
    use App\Http\Controllers\Admin\DashboardController;
    $revLabel = 'TOTAL TERKUMPUL · ' . strtoupper($now->locale('id')->shortMonthName . ' ' . $now->year);
@endphp

@section('hero')
    <div class="m-rev-label">{{ $revLabel }}</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $incomeThisMonth }}" data-m-fmt="rp-short">{{ DashboardController::rpShort($incomeThisMonth) }}</div>
        @if($delta)
            <span class="m-delta {{ $deltaUp ? '' : 'down' }}">{{ $delta }}</span>
        @endif
    </div>
    <div class="m-rev-sub">{{ number_format($paidCount, 0, ',', '.') }} lunas · target {{ DashboardController::rpShort($billedThisMonth) }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $totalPaid }}" data-m-fmt="rp-short">{{ $totalBilled > 0 ? DashboardController::rpShort($totalPaid) : number_format($paidCount, 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Terkumpul</div>
        <div class="m-kpi-sub">{{ number_format($paidCount, 0, ',', '.') }} lunas</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $activeStudents }}" data-m-fmt="int">{{ number_format($activeStudents, 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Siswa aktif</div>
        <div class="m-kpi-sub">Terdaftar</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $unpaidCount }}" data-m-fmt="int">{{ number_format($unpaidCount, 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Menunggu</div>
        <div class="m-kpi-sub">Perlu ditagih</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('m.dashboard') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari siswa, tagihan, INV" aria-label="Cari" oninput="document.querySelectorAll('[data-m-feed]').forEach(function(el){el.style.display = el.innerText.toLowerCase().includes(this.value.toLowerCase()) ? '' : 'none'}.bind(this))">
    </form>

    <div class="m-sec-head">
        <div class="m-sec-title">Menu Layanan</div>
        <a class="m-see" href="{{ route('m.siswa') }}">Kelola</a>
    </div>
    <div class="m-quick">
        <a class="m-qa" href="{{ route('m.siswa') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Siswa
        </a>
        <a class="m-qa" href="{{ route('m.tagihan') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/><path d="M9 7h6M9 11h6"/></svg>
            Tagihan
        </a>
        <a class="m-qa" href="{{ route('m.laporan') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>
            Laporan
        </a>
        <a class="m-qa" href="{{ route('m.bantuan') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
            Bantuan
        </a>
    </div>

    <div class="m-card">
        <div class="m-sec-head">
            <div class="m-sec-title">Pembayaran Terbaru</div>
            <span class="m-pill m-pill-ok">Live</span>
            <a class="m-see" href="{{ route('m.laporan') }}">Lihat semua</a>
        </div>
        @forelse($recent as $pay)
            <div class="m-feed" data-m-feed>
                <div class="m-fav">{{ strtoupper(substr($pay->student->name ?? '?', 0, 1)) }}</div>
                <div class="m-fmid">
                    <div class="m-fname">{{ $pay->student->name ?? 'Siswa' }}</div>
                    <div class="m-fdesc">{{ $pay->invoice_number }} · {{ $pay->bill->feeCategory->code ?? '' }} · {{ $pay->paid_at?->format('d M Y') }}</div>
                </div>
                <div class="m-famt">Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
            </div>
        @empty
            <div class="m-fdesc" style="padding:12px 0">Belum ada pembayaran sukses.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head">
            <div class="m-sec-title">Target Bulanan</div>
        </div>
        @php $pct = $billedThisMonth > 0 ? min(100, round($incomeThisMonth / $billedThisMonth * 100, 1)) : 0; @endphp
        <div class="m-fdesc"><span data-m-count="{{ $pct }}" data-m-fmt="pct1">{{ number_format($pct, 1, ',', '.') }}%</span> tercapai · {{ DashboardController::rpShort($incomeThisMonth) }} / {{ DashboardController::rpShort($billedThisMonth) }}</div>
        <div class="m-seg"><div data-m-seg="{{ $pct }}" style="width: {{ $pct }}%"></div></div>
    </div>
@endsection
