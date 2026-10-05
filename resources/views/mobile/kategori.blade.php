@extends('layouts.mobile')
@section('title', 'Kategori')
@section('breadcrumb', 'Master Data / Kategori')

@php use App\Http\Controllers\Admin\DashboardController; @endphp

@section('hero')
    <div class="m-rev-label">KATEGORI TAGIHAN</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $stats['total'] }}" data-m-fmt="int">{{ number_format($stats['total'], 0, ',', '.') }}</div>
        <span class="m-delta">{{ number_format($stats['aktif'], 0, ',', '.') }} aktif</span>
    </div>
    <div class="m-rev-sub">{{ DashboardController::rpShort($stats['revenue']) }} terkumpul · {{ number_format($stats['bills'], 0, ',', '.') }} tagihan</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['aktif'] }}" data-m-fmt="int">{{ number_format($stats['aktif'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Aktif</div>
        <div class="m-kpi-sub">Kategori</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['bills'] }}" data-m-fmt="int">{{ number_format($stats['bills'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Tagihan</div>
        <div class="m-kpi-sub">Total</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['revenue'] }}" data-m-fmt="rp-short">{{ DashboardController::rpShort($stats['revenue']) }}</div>
        <div class="m-kpi-lab">Terkumpul</div>
        <div class="m-kpi-sub">Nominal</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.kategori-tagihan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode, nama kategori" data-m-filter=".m-stu-card" aria-label="Cari kategori">
    </form>

    @forelse($categories as $c)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $c->code }} · {{ $c->name }}</div>
                    <div class="m-stu-sub">{{ $c->bills_count }} tagihan · terkumpul Rp {{ number_format($c->total_collected ?? 0, 0, ',', '.') }}</div>
                </div>
                @if($c->is_active)<span class="m-pill m-pill-ok">Aktif</span>@else<span class="m-pill m-pill-warn">Nonaktif</span>@endif
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada kategori.</div></div>
    @endforelse
@endsection
