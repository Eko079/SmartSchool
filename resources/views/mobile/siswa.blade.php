@extends('layouts.mobile')
@section('title', 'Data Siswa')
@section('breadcrumb', 'Master Data / Data Siswa')

@section('hero')
    <div class="m-rev-label">DATA SISWA · TA {{ $academicYear }}</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $stats['aktif'] }}" data-m-fmt="int">{{ number_format($stats['aktif'], 0, ',', '.') }}</div>
        <span class="m-delta">Aktif</span>
    </div>
    <div class="m-rev-sub">{{ number_format($stats['total'], 0, ',', '.') }} siswa terdaftar</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['total'] }}" data-m-fmt="int">{{ number_format($stats['total'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Total</div>
        <div class="m-kpi-sub">Semua status</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['baru'] }}" data-m-fmt="int">{{ number_format($stats['baru'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Baru</div>
        <div class="m-kpi-sub">Bulan ini</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $stats['nonaktif'] }}" data-m-fmt="int">{{ number_format($stats['nonaktif'], 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Nonaktif</div>
        <div class="m-kpi-sub">Cuti / lulus</div>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('m.siswa') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIS, nama, WA wali" data-m-filter=".m-stu-card" aria-label="Cari siswa">
    </form>

    <form method="GET" action="{{ route('m.siswa') }}" class="m-row2">
        <select name="class_id" class="m-select" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="status" class="m-select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
            <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
        </select>
    </form>

    <div class="m-sec-head">
        <div class="m-sec-title">Daftar Siswa</div>
        <span class="m-fdesc">{{ $students->total() }} data</span>
    </div>

    @forelse($students as $s)
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-av">{{ strtoupper(substr($s->name, 0, 1)) }}</div>
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $s->name }}</div>
                    <div class="m-stu-sub">NIS {{ $s->nis }} · {{ $s->classRoom->name ?? '-' }}</div>
                    <div class="m-stu-sub">WA wali: {{ $s->guardian_phone ?? '-' }}</div>
                </div>
                @if($s->status === 'aktif')
                    <span class="m-pill m-pill-ok">Aktif</span>
                @elseif($s->status === 'cuti')
                    <span class="m-pill m-pill-warn">Cuti</span>
                @else
                    <span class="m-pill m-pill-info">{{ ucfirst($s->status) }}</span>
                @endif
            </div>
            <div class="m-actions">
                <a class="m-btn" href="/admin/siswa/{{ $s->id }}/edit">Edit</a>
                <a class="m-btn" href="/admin/siswa?class_id={{ $s->class_id }}">Kelas</a>
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada data siswa.</div></div>
    @endforelse

    @if($students->hasPages())
        <div class="m-card">
            <div class="m-fdesc">{{ $students->firstItem() }}–{{ $students->lastItem() }} dari {{ $students->total() }}</div>
            <div class="m-actions">
                @if($students->previousPageUrl())
                    <a class="m-btn" href="{{ $students->previousPageUrl() }}">Kembali</a>
                @endif
                @if($students->nextPageUrl())
                    <a class="m-btn m-btn-primary" href="{{ $students->nextPageUrl() }}">Lanjut</a>
                @endif
            </div>
        </div>
    @endif
@endsection
