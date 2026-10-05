@extends('layouts.portal-mobile')
@section('title', 'Detail Tagihan')
@section('breadcrumb', 'Tagihan / ' . $bill->bill_code)

@section('hero')
    <div class="m-rev-label">{{ $bill->bill_code }} • {{ $bill->feeCategory->name ?? '-' }}</div>
    <div class="m-rev-row">
        <div class="m-rev-value">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
        @if($bill->status === 'paid')<span class="m-delta">Lunas</span>
        @elseif($bill->status === 'overdue')<span class="m-delta down">Menunggak</span>
        @else<span class="m-delta">Aktif</span>@endif
    </div>
    <div class="m-rev-sub">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }} • Order {{ $bill->order_id ?? '-' }}</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val">Rp</div>
        <div class="m-kpi-lab">Terbayar</div>
        <div class="m-kpi-sub">{{ number_format($bill->paid_amount, 0, ',', '.') }}</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">Rp</div>
        <div class="m-kpi-lab">Denda</div>
        <div class="m-kpi-sub">{{ number_format($bill->fine_amount, 0, ',', '.') }}</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">{{ $bill->period_month }}/{{ $bill->period_year }}</div>
        <div class="m-kpi-lab">Periode</div>
        <div class="m-kpi-sub">{{ $bill->feeCategory->name ?? '-' }}</div>
    </div>
@endsection

@section('content')
    @if($bill->va_number)
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Nomor VA</div></div>
        <div class="m-stu-name" style="font-size:20px;letter-spacing:1px">{{ $bill->va_number }}</div>
        <div class="m-fdesc">Berlaku hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }} • auto-callback Midtrans aktif</div>
    </div>
    @endif

    @if($bill->status !== 'paid')
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Bayar Sekarang</div></div>
        <form method="POST" action="{{ route('portal.tagihan.pay', $bill->id) }}">
            @csrf
            <div class="m-actions">
                <select name="method" class="m-btn" aria-label="Metode bayar">
                    <option value="bca_va">BCA VA</option>
                    <option value="bni_va">BNI VA</option>
                    <option value="qris">QRIS</option>
                </select>
                <button type="submit" class="m-btn m-btn-primary">Buat Pembayaran</button>
            </div>
        </form>
    </div>
    @endif

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Rincian</div><span class="m-see">Order {{ $bill->order_id ?? '-' }}</span></div>
        <div class="m-fdesc">Kategori: {{ $bill->feeCategory->name ?? '-' }}</div>
        <div class="m-fdesc">Siswa: {{ $bill->student->name ?? '-' }} • {{ $bill->student->classRoom->name ?? '-' }}</div>
        <div class="m-fdesc">WA Wali: {{ $bill->student->guardian_phone ?? '-' }}</div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Timeline</div></div>
        @forelse($bill->events as $ev)
            <div class="m-fdesc">{{ $ev->created_at?->format('d M Y H:i') }} • {{ $ev->message ?? $ev->event }}</div>
        @empty
            <div class="m-fdesc">Belum ada event.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Pembayaran</div></div>
        @forelse($bill->payments as $pay)
            <div class="m-feed">
                <div class="m-fmid">
                    <div class="m-fname">{{ $pay->invoice_number }} • Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                    <div class="m-fdesc">{{ $pay->payment_method }} • {{ $pay->status }}</div>
                </div>
                <a class="m-btn" href="{{ route('portal.kuitansi', $pay->id) }}">Kuitansi</a>
            </div>
        @empty
            <div class="m-fdesc">Belum ada pembayaran.</div>
        @endforelse
    </div>

    <div class="m-card"><div class="m-actions">
        <a class="m-btn" href="{{ route('portal.tagihan') }}">Kembali</a>
        <a class="m-btn" href="{{ route('portal.bantuan') }}">Hubungi Bendahara</a>
    </div></div>
@endsection
