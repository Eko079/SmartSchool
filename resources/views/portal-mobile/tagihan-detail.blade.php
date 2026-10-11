@extends('layouts.portal-mobile')
@section('title', 'Detail Tagihan')

@section('hero')
    <div class="m-rev-label">{{ $bill->bill_code }} • {{ $bill->display_name }}</div>
    <div class="m-rev-row">
        <div class="m-rev-value">Rp {{ number_format($bill->total_due, 0, ',', '.') }}</div>
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
        <div class="m-kpi-sub">{{ $bill->display_name }}</div>
    </div>
@endsection

@section('content')
    @if($bill->va_number)
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Nomor VA</div></div>
        <div class="m-stu-name" style="font-size:20px;letter-spacing:1px">{{ $bill->va_number }}</div>
        <div class="m-fdesc">Berlaku hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }} • lunas otomatis via callback Midtrans</div>
    </div>
    @endif
    @if($bill->qris_payload)
    <div class="m-card" style="text-align:center">
        <div class="m-sec-head"><div class="m-sec-title">QRIS • hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }}</div></div>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($bill->qris_payload) }}" width="200" height="200" alt="Kode QRIS" style="margin:8px auto;border-radius:8px;background:#fff;padding:8px">
        <div class="m-fdesc">Pindai dengan e-wallet / m-banking • lunas otomatis via callback Midtrans</div>
    </div>
    @endif

    @if(! empty($isArchived))
    <div class="m-card"><div class="m-fdesc">Akun arsip: pembayaran dinonaktifkan, hubungi operator.</div></div>
    @endif
    @if($bill->status !== 'paid' && empty($isArchived))
    @php
        $gw = $gateways ?? ['bca_va' => true, 'bni_va' => true, 'qris' => true];
        $gwLabels = ['bca_va' => 'BCA VA', 'bni_va' => 'BNI VA', 'qris' => 'QRIS'];
    @endphp
    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Bayar Sekarang</div></div>
        @if(! ($gw['bca_va'] ?? false) && ! ($gw['bni_va'] ?? false) && ! ($gw['qris'] ?? false))
        <div class="m-fdesc">Pembayaran online sedang nonaktif, hubungi bendahara.</div>
        @else
        <div class="m-actions">
            <select id="pay-method" class="m-btn" aria-label="Metode bayar">
                @foreach($gwLabels as $val => $label)
                    @if(! empty($gw[$val]))<option value="{{ $val }}">{{ $label }}</option>@endif
                @endforeach
            </select>
            <button type="button" id="pay-submit" class="m-btn m-btn-primary" onclick="submitDetailPay()">Bayar</button>
        </div>
        @if($bill->order_id)
        <div class="m-actions" style="margin-top:8px">
            <button type="button" id="pay-check" class="m-btn" style="flex:1" onclick="checkDetailPay()">Saya Sudah Bayar (Cek Status)</button>
        </div>
        @endif
        <div class="m-fdesc" id="pay-msg" style="display:none"></div>
        <div class="m-card" id="pay-result" style="display:none;text-align:center">
            <div class="m-sec-title" id="pay-result-label">Nomor bayar</div>
            <div class="m-stu-name" id="pay-result-va">-</div>
            <img id="pay-result-qr" style="display:none;margin:8px auto;border-radius:8px;background:#fff;padding:8px" width="200" height="200" alt="Kode QRIS">
            <div class="m-fdesc" id="pay-result-order"></div>
        </div>
        @endif
    </div>
    <script>
    function submitDetailPay() {
        var submit = document.getElementById('pay-submit');
        if (submit.disabled) return;
        submit.disabled = true; submit.textContent = 'Memproses...';
        fetch('{{ route('portal.tagihan.pay', $bill->id) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ method: document.getElementById('pay-method').value })
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            var msg = document.getElementById('pay-msg');
            msg.style.display = '';
            if (!res.ok) { msg.textContent = res.body.message || 'Gagal membuat pembayaran.'; submit.disabled = false; submit.textContent = 'Bayar'; return; }
            msg.textContent = 'Order ' + (res.body.transaction.order_id || '-') + ' dibuat.';
            var box = document.getElementById('pay-result');
            box.style.display = '';
            var qrImg = document.getElementById('pay-result-qr');
            if (res.body.transaction.qr_string) {
                document.getElementById('pay-result-label').textContent = 'Pindai QRIS';
                document.getElementById('pay-result-va').textContent = '';
                qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(res.body.transaction.qr_string);
                qrImg.style.display = 'block';
            } else {
                document.getElementById('pay-result-label').textContent = 'Nomor bayar';
                document.getElementById('pay-result-va').textContent = res.body.transaction.va_number || '-';
                qrImg.style.display = 'none';
            }
            document.getElementById('pay-result-order').textContent = 'Order ' + (res.body.transaction.order_id || '-');
            submit.disabled = false; submit.textContent = 'Bayar';
        }).catch(function () { ssAlert('Gagal membuat pembayaran.'); submit.disabled = false; submit.textContent = 'Bayar'; });
    }
    function checkDetailPay() {
        var btn = document.getElementById('pay-check');
        if (btn.disabled) return;
        btn.disabled = true; btn.textContent = 'Mengecek...';
        fetch('{{ route('portal.tagihan.status', $bill->id) }}', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            var msg = document.getElementById('pay-msg');
            msg.style.display = '';
            if (!res.ok) { msg.textContent = res.body.message || 'Gagal cek status.'; }
            else if (res.body.settled) { msg.textContent = 'Pembayaran lunas. Memuat ulang...'; setTimeout(function () { location.reload(); }, 1500); return; }
            else { msg.textContent = 'Status: ' + (res.body.status || '-') + '. Selesaikan pembayaran lalu tekan lagi.'; }
            btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)';
        }).catch(function () { ssAlert('Gagal cek status.'); btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)'; });
    }
    </script>
    @endif

    <div class="m-card">
        <div class="m-sec-head"><div class="m-sec-title">Rincian</div></div>
        <div class="m-kv"><span>Order</span><b>{{ $bill->order_id ?? '-' }}</b></div>
        <div class="m-kv"><span>Kategori</span><b>{{ $bill->display_name }}</b></div>
        <div class="m-kv"><span>Siswa</span><b>{{ $bill->student->name ?? '-' }}</b></div>
        <div class="m-kv"><span>Kelas</span><b>{{ $bill->student->classRoom->name ?? '-' }}</b></div>
        <div class="m-kv"><span>Kontak Wali</span><b>{{ $bill->student->guardian_phone ?? '-' }}</b></div>
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
            <div style="padding:10px 0;border-top:1px solid var(--m-border)">
                <div class="m-kv"><span>Order</span><b>{{ $pay->invoice_number }}</b></div>
                <div class="m-kv"><span>Total Biaya</span><b>Rp {{ number_format($pay->amount, 0, ',', '.') }}</b></div>
                <div class="m-kv"><span>Jenis Pembayaran</span><b>{{ $pay->method_label }}</b></div>
                <div class="m-kv"><span>Status</span>
                    @if($pay->status === 'success')<span class="m-pill m-pill-ok">{{ $pay->status_label }}</span>
                    @elseif($pay->status === 'pending')<span class="m-pill m-pill-warn">{{ $pay->status_label }}</span>
                    @else<span class="m-pill m-pill-err">{{ $pay->status_label }}</span>@endif
                </div>
                <a class="m-btn" style="width:100%;margin-top:8px" href="{{ route('portal.kuitansi', $pay->id) }}">Kuitansi</a>
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
