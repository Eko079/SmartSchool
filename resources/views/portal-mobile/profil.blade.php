@extends('layouts.portal-mobile')
@section('title', 'Profil Anak')
@section('breadcrumb', 'Profil / ' . $student->name)

@section('hero')
    <div class="m-rev-label">NIS {{ $student->nis }} • {{ $student->classRoom->name ?? '-' }}</div>
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">{{ $student->name }}</div></div>
    <div class="m-rev-sub">Masuk {{ $student->entry_year ?? '-' }} • {{ ucfirst($student->status) }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $student->bills->count() }}" data-m-fmt="int">{{ number_format($student->bills->count(), 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Invoice</div>
        <div class="m-kpi-sub">Total</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $student->bills->where('status', 'paid')->count() }}" data-m-fmt="int">{{ number_format($student->bills->where('status', 'paid')->count(), 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Lunas</div>
        <div class="m-kpi-sub">Selesai</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val" data-m-count="{{ $student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->count() }}" data-m-fmt="int">{{ number_format($student->bills->whereIn('status', ['unpaid', 'partial', 'overdue'])->count(), 0, ',', '.') }}</div>
        <div class="m-kpi-lab">Aktif</div>
        <div class="m-kpi-sub">Menunggu</div>
    </div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Data Anak</div></div>
        <div class="m-stu-sub">NISN: {{ $student->nisn ?? '-' }}</div>
        <div class="m-stu-sub">Email: {{ $student->email ?? '-' }}</div>
        <div class="m-stu-sub">WA Wali: {{ $student->guardian_phone ?? '-' }} ({{ $student->guardian_name ?? '-' }})</div>
        <div class="m-stu-sub">Alamat: {{ $student->address ?? '-' }}</div>
    </div>

    <div class="m-sec-head"><div class="m-sec-title">Riwayat Invoice</div><span class="m-see">klik buka detail</span></div>
    @forelse($student->bills->sortByDesc('due_date')->take(8) as $bill)
        <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="m-card m-stu-card" style="display:block;text-decoration:none;color:inherit">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $bill->feeCategory->name ?? '-' }} • Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                    <div class="m-stu-sub">{{ $bill->bill_code }} • {{ $bill->due_date?->format('d M Y') ?? '-' }}</div>
                </div>
                @if($bill->status === 'paid')<span class="m-pill m-pill-ok">Lunas</span>
                @elseif($bill->status === 'overdue')<span class="m-pill m-pill-err">Menunggak</span>
                @else<span class="m-pill m-pill-warn">{{ ucfirst($bill->status) }}</span>@endif
            </div>
        </a>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada invoice.</div></div>
    @endforelse
@endsection
