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
    @php $payOf = fn($b) => $b->payments ? $b->payments->sortByDesc('paid_at')->first() : null; @endphp
    <form method="GET" action="{{ route('portal.tagihan') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari invoice, SPP, LAB…" data-m-filter=".m-stu-card" aria-label="Cari tagihan">
    </form>

    <form method="GET" action="{{ route('portal.tagihan') }}" class="m-card">
        <div class="m-actions">
            <select name="academic_year" onchange="this.form.submit()" class="m-btn" aria-label="Filter tahun ajaran">
                @foreach(($academicYears ?? collect()) as $ta)
                <option value="{{ $ta }}" {{ ($selYear ?? request('academic_year')) === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            <select name="semester" onchange="this.form.submit()" class="m-btn" aria-label="Filter semester">
                <option value="ganjil" {{ ($selSemester ?? 'ganjil') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ ($selSemester ?? '') === 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="m-btn" aria-label="Filter status">
                @foreach(['all' => 'Semua', 'unpaid' => 'Belum bayar', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'overdue' => 'Menunggak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <a class="m-btn m-btn-primary" href="{{ route('portal.riwayat') }}">Riwayat</a>
        </div>
        <div class="m-fdesc">TA {{ $selYear ?? '-' }} • Semester {{ isset($selSemester) ? ucfirst($selSemester) : '-' }} saja.</div>
    </form>

    <div class="m-sec-head"><div class="m-sec-title">Tagihan {{ isset($selSemester) ? ucfirst($selSemester) : '' }}</div><span class="m-see">{{ $bills->total() }} data ›</span></div>
    @forelse($bills as $i => $bill)
        @php $pay = $payOf($bill); $isPaid = $bill->status === 'paid'; $statusLabel = $bill->status_label; $methodLabel = $pay ? $pay->method_label : '-'; @endphp
        <div class="m-card m-stu-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ ($bills->firstItem() ?? 1) + $i }}. {{ $bill->bill_code }}</div>
                    <div class="m-stu-sub">{{ $bill->feeCategory->name ?? '-' }} • Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                    <div class="m-stu-sub">{{ $statusLabel }} • {{ $pay?->paid_at?->format('d M Y') ?? '-' }} • {{ $methodLabel }}</div>
                </div>
                @if($isPaid)<span class="m-pill m-pill-ok">Lunas</span>
                @else<span class="m-pill m-pill-warn">Belum bayar</span>@endif
            </div>
            <div class="m-actions">
                <button type="button" class="m-btn" onclick="openPayModal({{ $bill->id }}, this, true)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->feeCategory->name ?? '-' }}"
                    data-amount="Rp {{ number_format($bill->amount, 0, ',', '.') }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    data-tanggal="{{ $pay?->paid_at?->format('d M Y H:i') ?? '-' }}"
                    data-tempat="{{ $methodLabel }}">Detail</button>
                @if(! $isPaid)
                <button type="button" class="m-btn m-btn-primary" onclick="openPayModal({{ $bill->id }}, this)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->feeCategory->name ?? '-' }}"
                    data-amount="Rp {{ number_format($bill->amount, 0, ',', '.') }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="BELUM BAYAR">Bayar</button>
                @endif
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Tidak ada tagihan pada semester ini.</div></div>
    @endforelse

    <div id="pay-modal" class="m-card hidden" style="position:fixed;inset:auto 12px 76px 12px;z-index:60">
        <div class="m-sec-head"><div class="m-sec-title" id="pm-code">-</div><button type="button" class="m-btn" onclick="closePayModal()">Tutup</button></div>
        <div class="m-fdesc" id="pm-jenis">-</div>
        <div class="m-fdesc">Jumlah <b id="pm-amount">-</b> • Tempo <b id="pm-due">-</b> • <b id="pm-status">-</b></div>
        <div class="m-fdesc" id="pm-history">-</div>
        <div id="pm-paybox">
            <div class="m-sec-title">Silakan pilih metode pembayaran</div>
            <div class="m-actions">
                <label class="m-btn"><input type="radio" name="pm_method" value="bca_va" checked> BCA VA</label>
                <label class="m-btn"><input type="radio" name="pm_method" value="bni_va"> BNI VA</label>
                <label class="m-btn"><input type="radio" name="pm_method" value="qris"> QRIS</label>
            </div>
            <div class="m-fdesc" id="pm-result" style="display:none"></div>
        </div>
        <div class="m-actions">
            <a href="#" id="pm-detail-link" class="m-btn">Halaman Detail</a>
            <button type="button" id="pm-submit" class="m-btn m-btn-primary" onclick="submitPayModal()">Buat Pembayaran</button>
        </div>
    </div>

    <script>
    var pmBillId = null;
    function openPayModal(id, btn, detailOnly) {
        pmBillId = id;
        var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '-') : '-'; };
        document.getElementById('pm-code').textContent = g('code');
        document.getElementById('pm-jenis').textContent = g('jenis');
        document.getElementById('pm-amount').textContent = g('amount');
        document.getElementById('pm-due').textContent = g('due');
        document.getElementById('pm-status').textContent = g('status');
        document.getElementById('pm-history').textContent = (g('tanggal') !== '-' ? g('tanggal') + ' • ' + g('tempat') : '-');
        document.getElementById('pm-detail-link').href = '/tagihan/' + id;
        var isPaid = g('status') === 'LUNAS';
        document.getElementById('pm-paybox').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-submit').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-result').style.display = 'none';
        document.getElementById('pay-modal').classList.remove('hidden');
    }
    function closePayModal() { document.getElementById('pay-modal').classList.add('hidden'); }
    function submitPayModal() {
        var checked = document.querySelector('input[name="pm_method"]:checked');
        var method = checked ? checked.value : 'bca_va';
        fetch('/tagihan/' + pmBillId + '/bayar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ method: method })
        }).then(function (r) { return r.json(); }).then(function (j) {
            var el = document.getElementById('pm-result');
            el.textContent = 'VA: ' + (j.transaction.va_number || '-') + ' • Order: ' + (j.transaction.order_id || '-');
            el.style.display = '';
            setTimeout(function () { location.reload(); }, 1200);
        }).catch(function () { alert('Gagal membuat pembayaran.'); });
    }
    </script>
@endsection
