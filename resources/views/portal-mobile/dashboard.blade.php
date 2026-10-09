@extends('layouts.portal-mobile')
@section('title', 'Beranda')
@section('breadcrumb', 'Beranda / Dashboard')

@php
    $rpShort = function ($v) {
        $v = (float) $v;
        if ($v >= 1000000000) return 'Rp ' . rtrim(rtrim(number_format($v / 1000000000, 1, ',', '.'), '0'), ',') . ' M';
        if ($v >= 1000000) return 'Rp ' . rtrim(rtrim(number_format($v / 1000000, 1, ',', '.'), '0'), ',') . ' Jt';
        if ($v >= 1000) return 'Rp ' . rtrim(rtrim(number_format($v / 1000, 1, ',', '.'), '0'), ',') . ' rb';
        return 'Rp ' . number_format($v, 0, ',', '.');
    };
@endphp

@section('hero')
    <div class="m-rev-label">MENUNGGU DIBAYAR • {{ $student->name }}</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $stats['waiting_amount'] }}" data-m-fmt="rp-short">{{ $rpShort($stats['waiting_amount']) }}</div>
        <span class="m-delta {{ $stats['overdue_count'] > 0 ? 'down' : '' }}">{{ $stats['active_count'] }} aktif</span>
    </div>
    <div class="m-rev-sub">{{ $student->classRoom->name ?? '-' }} • jatuh tempo {{ $stats['nearest_due']?->due_date?->format('d M') ?? '-' }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['active_count'] }}" data-m-fmt="int">{{ number_format($stats['active_count'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Tagihan aktif</div>
        <div class="m-kpi-sub">Menunggu</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['paid_count'] }}" data-m-fmt="int">{{ number_format($stats['paid_count'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Sudah lunas</div>
        <div class="m-kpi-sub">Kuitansi ada</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['overdue_count'] }}" data-m-fmt="int">{{ number_format($stats['overdue_count'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Tunggakan</div>
        <div class="m-kpi-sub">Segera bayar</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('portal.tagihan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" placeholder="Cari invoice, SPP…" aria-label="Cari tagihan" data-m-filter=".m-stu-card">
    </form>

    <div class="m-sec-head">
        <div class="m-sec-title">Menu Layanan</div>
        <a class="m-see" href="{{ route('portal.tagihan') }}">Lihat ›</a>
    </div>
    <div class="m-quick">
        <a class="m-qa" href="{{ route('portal.tagihan') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/><path d="M9 7h6M9 11h6"/></svg>
            Bayar
        </a>
        <a class="m-qa" href="{{ route('portal.riwayat') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>
            Riwayat
        </a>
        <a class="m-qa" href="{{ route('portal.profil') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Profil
        </a>
        <a class="m-qa" href="{{ route('portal.bantuan') }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
            Bantuan
        </a>
    </div>

    <div class="m-sec-head">
        <div class="m-sec-title">Tagihan Terbaru</div>
        <a class="m-see" href="{{ route('portal.tagihan') }}">Lihat ›</a>
    </div>
    @forelse($latest_bills as $bill)
        <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="m-card m-stu-card" style="display:block;text-decoration:none;color:inherit">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $bill->bill_code }} • {{ $bill->feeCategory->name ?? '-' }}</div>
                    <div class="m-stu-sub">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }} • Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                </div>
                @if($bill->status === 'paid')<span class="m-pill m-pill-ok">Lunas</span>
                @elseif($bill->status === 'overdue')<span class="m-pill m-pill-err">Menunggak</span>
                @elseif($bill->status === 'partial')<span class="m-pill m-pill-warn">Sebagian</span>
                @else<span class="m-pill m-pill-warn">Belum bayar</span>@endif
            </div>
        </a>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada tagihan.</div></div>
    @endforelse

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Per Semester</div></div>
        @forelse(($per_semester ?? collect()) as $label => $row)
            <div class="m-fdesc">{{ $label }} • Rp {{ number_format($row['total'], 0, ',', '.') }} • {{ $row['count'] }} tagihan</div>
            <div class="m-seg"><div data-m-seg="{{ $per_semester->max('total') > 0 ? round($row['total'] / $per_semester->max('total') * 100, 1) : 0 }}" style="width: {{ $per_semester->max('total') > 0 ? round($row['total'] / $per_semester->max('total') * 100, 1) : 0 }}%"></div></div>
        @empty
            <div class="m-fdesc">Belum ada data semester.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Per Kategori</div></div>
        @forelse($per_category as $name => $total)
            @php $pct = $per_category->sum() > 0 ? round($total / $per_category->sum() * 100, 1) : 0; @endphp
            <div class="m-fdesc">{{ $name }} • {{ $rpShort($total) }} • {{ number_format($pct, 1, ',', '.') }}%</div>
            <div class="m-seg"><div data-m-seg="{{ $pct }}" style="width: {{ $pct }}%"></div></div>
        @empty
            <div class="m-fdesc">Belum ada data kategori.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Aktivitas</div></div>
        @forelse($activities as $ev)
            <div class="m-feed" data-m-feed>
                <div class="m-fav">•</div>
                <div class="m-fmid">
                    <div class="m-fname">{{ $ev->message ?? $ev->event }}</div>
                    <div class="m-fdesc">{{ $ev->created_at?->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="m-fdesc">Belum ada aktivitas.</div>
        @endforelse
        @if($stats['nearest_due'])
            <div class="m-fdesc">Batas: {{ $stats['nearest_due']->bill_code }} jatuh tempo {{ $stats['nearest_due']->due_date?->format('d M Y') }}</div>
        @endif
    </div>
@endsection
