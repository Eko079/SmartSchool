@extends('layouts.portal-mobile')
@section('title', 'Bantuan')
@section('breadcrumb', 'Lainnya / Bantuan')

@section('hero')
    <div class="m-rev-label">PUSAT BANTUAN</div>
    <div class="m-rev-row"><div class="m-rev-value" style="font-size:26px">Cara bayar?</div></div>
    <div class="m-rev-sub">VA / QRIS & unduh kuitansi</div>
@endsection

@section('kpi')
    <div class="m-kpi-card">
        <div class="m-kpi-val">VA</div>
        <div class="m-kpi-lab">Bayar</div>
        <div class="m-kpi-sub">BCA / BNI</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">QRIS</div>
        <div class="m-kpi-lab">Scan</div>
        <div class="m-kpi-sub">m-banking</div>
    </div>
    <div class="m-kpi-card">
        <div class="m-kpi-val">PDF</div>
        <div class="m-kpi-lab">Kuitansi</div>
        <div class="m-kpi-sub">Unduh</div>
    </div>
@endsection

@section('content')
    <div class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari: bayar VA, QRIS, kuitansi" data-m-filter=".m-faq" aria-label="Cari bantuan">
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Panduan Cepat</div></div>
        <div class="m-actions">
            <a class="m-btn" href="{{ route('portal.tagihan') }}">Bayar</a>
            <a class="m-btn" href="{{ route('portal.riwayat') }}">Kuitansi</a>
            <a class="m-btn" href="{{ route('portal.profil') }}">Profil</a>
        </div>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">FAQ</div></div>
        @forelse($faqs as $faq)
            <div class="m-faq"><div class="m-stu-name">{{ $faq->question }}</div><div class="m-stu-sub">{{ $faq->answer }}</div></div>
        @empty
            <div class="m-fdesc">Belum ada FAQ.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Buat Tiket</div><span class="m-see">SLA 1x24 jam</span></div>
        <form method="POST" action="{{ route('portal.bantuan.store') }}">
            @csrf
            <label class="m-label">Subjek</label>
            <input type="text" name="subject" class="m-input" required>
            <label class="m-label" style="margin-top:8px">Kendala</label>
            <textarea name="message" rows="3" class="m-input" required></textarea>
            <div class="m-actions" style="margin-top:10px">
                <button type="submit" class="m-btn m-btn-primary">Kirim Tiket</button>
            </div>
        </form>
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Tiket Saya</div></div>
        @forelse($tickets as $ticket)
            <div class="m-faq"><div class="m-stu-name">{{ $ticket->ticket_number }} • {{ $ticket->subject }}</div><div class="m-stu-sub">{{ $ticket->message }} • {{ ucfirst($ticket->status) }}</div></div>
        @empty
            <div class="m-fdesc">Belum ada tiket.</div>
        @endforelse
    </div>

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Kontak</div></div>
        <div class="m-stu-sub">Telepon: {{ $siteBrand['school_phone'] ?? '-' }} • 08.00–17.00</div>
        <div class="m-stu-sub">Email: {{ $siteBrand['school_email'] ?? '-' }}</div>
    </div>
@endsection
