@extends('layouts.mobile')
@section('title', 'Laporan')
@section('breadcrumb', 'Laporan / Pembayaran')

@php use App\Http\Controllers\Admin\DashboardController; @endphp

@section('hero')
    <div class="m-rev-label">TOTAL TERKUMPUL</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $totalIncome }}" data-m-fmt="rp-short">{{ DashboardController::rpShort($totalIncome) }}</div>
        <span class="m-delta" data-m-count="{{ $rate }}" data-m-fmt="pct1">{{ $rate }}%</span>
    </div>
    <div class="m-rev-sub">Kolektibilitas {{ $rate }}% · {{ number_format($successCount, 0, ',', '.') }} sukses</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $successCount }}" data-m-fmt="int">{{ number_format($successCount, 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Sukses</div>
        <div class="m-kpi-sub">Transaksi</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $rate }}" data-m-fmt="pct1">{{ $rate }}%</div>
        <div class="m-kpi-lab">Kolektibilitas</div>
        <div class="m-kpi-sub">Target 90%</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $totalIncome }}" data-m-fmt="rp-short">{{ DashboardController::rpShort($totalIncome) }}</div>
        <div class="m-kpi-lab">Nominal</div>
        <div class="m-kpi-sub">Terkumpul</div>
    </div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Tren 6 Bulan</div></div>
        <div style="display:flex;align-items:flex-end;gap:6px;height:110px;margin-top:10px">
            @foreach($trend as $i => $v)
                @php $h = $maxTrend > 0 ? max(6, round($v / $maxTrend * 100)) : 6; $px = round($h / 100 * 86); @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                    <div data-m-bar="{{ $px }}" style="width:100%;background:#2563EB;border-radius:6px;height:{{ $px }}px" title="{{ $labels[$i] }}: Rp {{ number_format($v, 0, ',', '.') }}"></div>
                    <div class="m-fdesc">{{ $labels[$i] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <form method="GET" action="{{ route('admin.laporan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode, INV, nama siswa" data-m-filter=".m-stu-card" aria-label="Cari laporan">
    </form>

    <div class="m-sec-head"><div class="m-sec-title">Pembayaran</div></div>
    @forelse($payments as $p)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $p->invoice_number }}</div>
                    <div class="m-stu-sub">{{ $p->student->name ?? '-' }} · {{ $p->method_label ?? '-' }}</div>
                    <div class="m-stu-sub">{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
                <div class="m-famt">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada pembayaran.</div></div>
    @endforelse

    @if($payments->hasPages())
        <div class="m-card"><div class="m-actions">
            @if($payments->previousPageUrl())<a class="m-btn" href="{{ $payments->previousPageUrl() }}">Kembali</a>@endif
            @if($payments->nextPageUrl())<a class="m-btn m-btn-primary" href="{{ $payments->nextPageUrl() }}">Lanjut</a>@endif
        </div></div>
    @endif
@endsection
