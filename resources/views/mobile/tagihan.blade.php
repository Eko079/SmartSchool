@extends('layouts.mobile')
@section('title', 'Tagihan')
@section('breadcrumb', 'Billing / Tagihan')

@php use App\Http\Controllers\Admin\DashboardController; @endphp

@section('hero')
    <div class="m-rev-label">TAGIHAN AKTIF</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $stats['total'] }}" data-m-fmt="int">{{ number_format($stats['total'], 0, ',', '.') }}</div>
        <span class="m-delta {{ $stats['tunggakan'] > 0 ? 'down' : '' }}">{{ number_format($stats['lunas'], 0, ',', '.') }} lunas</span>
    </div>
    <div class="m-rev-sub">{{ DashboardController::rpShort($stats['nominal']) }} total nominal</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['lunas'] }}" data-m-fmt="int">{{ number_format($stats['lunas'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Lunas</div>
        <div class="m-kpi-sub">Selesai</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['tunggakan'] }}" data-m-fmt="int">{{ number_format($stats['tunggakan'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Tunggakan</div>
        <div class="m-kpi-sub">Perlu ditagih</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $categories->count() }}" data-m-fmt="int">{{ $categories->count() }}</div>
        <div class="m-kpi-lab">Kategori</div>
        <div class="m-kpi-sub">Aktif</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('m.tagihan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode, INV, nama siswa" data-m-filter=".m-stu-card" aria-label="Cari tagihan">
    </form>

    <div class="m-sec-head"><div class="m-sec-title">Kategori</div></div>
    @forelse($categories as $c)
        <div class="m-card">
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

    <div class="m-sec-head"><div class="m-sec-title">Tagihan Terbaru</div></div>
    @forelse($bills as $b)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $b->bill_code }}</div>
                    <div class="m-stu-sub">{{ $b->student->name ?? '-' }} · {{ $b->feeCategory->name ?? '-' }}</div>
                    <div class="m-stu-sub">Rp {{ number_format($b->amount, 0, ',', '.') }} · {{ $b->period_month }}/{{ $b->period_year }}</div>
                </div>
                @if($b->status === 'paid')<span class="m-pill m-pill-ok">Lunas</span>
                @elseif($b->status === 'overdue')<span class="m-pill m-pill-err">Menunggak</span>
                @elseif($b->status === 'partial')<span class="m-pill m-pill-warn">Sebagian</span>
                @else<span class="m-pill m-pill-warn">Belum bayar</span>@endif
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada tagihan.</div></div>
    @endforelse

    @if($bills->hasPages())
        <div class="m-card"><div class="m-actions">
            @if($bills->previousPageUrl())<a class="m-btn" href="{{ $bills->previousPageUrl() }}">Kembali</a>@endif
            @if($bills->nextPageUrl())<a class="m-btn m-btn-primary" href="{{ $bills->nextPageUrl() }}">Lanjut</a>@endif
        </div></div>
    @endif

    <div class="m-sec-head"><div class="m-sec-title">Riwayat Generate</div></div>
    @forelse($history as $h)
        <div class="m-card"><div class="m-stu-sub">{{ $h->feeCategory->code ?? '-' }} · {{ $h->period_month }}/{{ $h->period_year }} · {{ $h->total }} tagihan · Rp {{ number_format($h->nominal, 0, ',', '.') }}</div></div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada riwayat generate.</div></div>
    @endforelse
@endsection
