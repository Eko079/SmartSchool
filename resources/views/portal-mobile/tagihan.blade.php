@extends('layouts.portal-mobile')
@section('title', 'Tagihan Saya')
@section('breadcrumb', 'Tagihan / Tagihan Saya')

@section('hero')
    <div class="m-rev-label">TOTAL MENUNGGU • {{ number_format($bills->total(), 0, ',', '.') }} TAGIHAN</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $summary['waiting'] }}" data-m-fmt="rp-short">Rp {{ number_format($summary['waiting'], 0, ',', '.') }}</div>
    </div>
    <div class="m-rev-sub">Lunas Rp {{ number_format($summary['paid'], 0, ',', '.') }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $bills->total() }}" data-m-fmt="int">{{ number_format($bills->total(), 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Tagihan</div>
        <div class="m-kpi-sub">Ditampilkan</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">Rp</div>
        <div class="m-kpi-lab">Menunggu</div>
        <div class="m-kpi-sub">{{ number_format($summary['waiting'], 0, ',', '.') }}</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">Rp</div>
        <div class="m-kpi-lab">Lunas</div>
        <div class="m-kpi-sub">{{ number_format($summary['paid'], 0, ',', '.') }}</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('portal.tagihan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari invoice, SPP, LAB…" data-m-filter=".m-stu-card" aria-label="Cari tagihan">
    </form>

    <form method="GET" action="{{ route('portal.tagihan') }}" class="m-card">
        <div class="m-actions">
            <select name="academic_year" onchange="this.form.submit()" class="m-btn" aria-label="Filter tahun ajaran">
                <option value="">Semua TA</option>
                @foreach(($academicYears ?? collect()) as $ta)
                <option value="{{ $ta }}" {{ request('academic_year') === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            <select name="semester" onchange="this.form.submit()" class="m-btn" aria-label="Filter semester">
                <option value="">Semua Smt</option>
                <option value="ganjil" {{ request('semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ request('semester') === 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="m-btn" aria-label="Filter status">
                @foreach(['all' => 'Semua', 'unpaid' => 'Belum bayar', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'overdue' => 'Menunggak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <a class="m-btn m-btn-primary" href="{{ route('portal.riwayat') }}">Riwayat</a>
        </div>
    </form>

    <div class="m-sec-head"><div class="m-sec-title">Tagihan Terbaru</div><span class="m-see">{{ $bills->total() }} data ›</span></div>
    @forelse($bills as $bill)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $bill->bill_code }} • {{ $bill->feeCategory->name ?? '-' }}</div>
                    <div class="m-stu-sub">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }} • Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                    @if($bill->academic_year || $bill->semester)
                    <div class="m-stu-sub">TA {{ $bill->academic_year ?? '-' }} • {{ $bill->semester ? ucfirst($bill->semester) : '-' }}</div>
                    @endif
                </div>
                @if($bill->status === 'paid')<span class="m-pill m-pill-ok">Lunas</span>
                @elseif($bill->status === 'overdue')<span class="m-pill m-pill-err">Menunggak</span>
                @elseif($bill->status === 'partial')<span class="m-pill m-pill-warn">Sebagian</span>
                @else<span class="m-pill m-pill-warn">Belum bayar</span>@endif
            </div>
            <div class="m-actions">
                <a class="m-btn" href="{{ route('portal.tagihan.show', $bill->id) }}">Detail</a>
                @if($bill->status !== 'paid')
                <form method="POST" action="{{ route('portal.tagihan.pay', $bill->id) }}">
                    @csrf
                    <input type="hidden" name="method" value="bca_va">
                    <button type="submit" class="m-btn m-btn-primary">Bayar</button>
                </form>
                @endif
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Tidak ada tagihan yang cocok.</div></div>
    @endforelse

    @if($bills->hasPages())
        <div class="m-card"><div class="m-actions">
            @if($bills->previousPageUrl())<a class="m-btn" href="{{ $bills->previousPageUrl() }}">Kembali</a>@endif
            @if($bills->nextPageUrl())<a class="m-btn m-btn-primary" href="{{ $bills->nextPageUrl() }}">Lanjut</a>@endif
        </div></div>
    @endif
@endsection
