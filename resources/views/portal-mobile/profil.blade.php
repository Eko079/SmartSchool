@extends('layouts.portal-mobile')
@section('title', 'Profile')

@section('hero')
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Profile</div></div>
@endsection

@section('content')
    <div class="m-card">
        <div class="m-sec-head" style="margin-bottom:8px"><div class="m-sec-title">Profile</div></div>
        <div class="m-kv"><span>NISN</span><b>{{ $student->nisn ?? '-' }}</b></div>
        <div class="m-kv"><span>Email</span><b style="word-break:break-all">{{ $student->email ?? '-' }}</b></div>
        <div class="m-kv"><span>No. WA</span><b>{{ $student->guardian_phone ?? '-' }}</b></div>
        <div class="m-kv"><span>Alamat</span><b>{{ $student->address ?? '-' }}</b></div>
        <div class="m-kv"><span>Tahun Masuk</span><b>{{ $student->entry_year ?? '-' }}</b></div>
        <div class="m-kv"><span>Status Siswa</span><b>{{ ucfirst($student->status) }}</b></div>
    </div>

    <div class="m-sec-head"><div class="m-sec-title">Riwayat Tagihan</div><span class="m-see">klik untuk detail</span></div>
    @forelse($student->bills->sortByDesc('due_date')->take(8) as $bill)
        <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="m-card m-stu-card" style="display:block;text-decoration:none;color:inherit">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $bill->display_name }}</div>
                    <div class="m-stu-sub">{{ $bill->bill_code }}</div>
                    @if($bill->status === 'paid')<div class="m-stu-sub m-paid">Telah dibayar</div>
                    @else<div class="m-stu-sub m-due">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }}</div>@endif
                </div>
                <div class="m-stu-right">
                    @if($bill->status === 'paid')<span class="m-pill m-pill-ok">Lunas</span>
                    @elseif($bill->status === 'overdue')<span class="m-pill m-pill-err">Menunggak</span>
                    @else<span class="m-pill m-pill-warn">{{ $bill->status_label }}</span>@endif
                    <div class="m-stu-price">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </a>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada tagihan.</div></div>
    @endforelse
@endsection
