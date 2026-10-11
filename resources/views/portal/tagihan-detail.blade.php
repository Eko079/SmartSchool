@extends('layouts.portal')
@section('title', 'Detail Tagihan')
@section('page-title', $bill->bill_code)

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card space-y-3">
        <div class="flex flex-wrap items-center gap-3">
            <x-ss-pill status="{{ $bill->status }}" label="{{ $bill->status_label }}" />
            <span class="text-xs text-slate-400">Order {{ $bill->order_id ?? '-' }}</span>
        </div>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori</dt>
                <dd class="mt-1 font-semibold">{{ $bill->display_name }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Periode</dt>
                <dd class="mt-1 font-semibold">{{ $bill->period_month }}/{{ $bill->period_year }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</dt>
                <dd class="mt-1 font-semibold">{{ $bill->display_year ?? '-' }}{{ $bill->semester ? ' • ' . ucfirst($bill->semester) : '' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nominal</dt>
                <dd class="mt-1 font-semibold">{{ $rp($bill->amount) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Terbayar</dt>
                <dd class="mt-1 font-semibold">{{ $rp($bill->paid_amount) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Denda</dt>
                <dd class="mt-1 font-semibold text-rose-600">{{ $rp($bill->fine_amount) }}</dd>
            </div>
            <div class="rounded-xl bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-blue-500">Total Wajib Bayar</dt>
                <dd class="mt-1 font-bold text-blue-700 dark:text-blue-300">{{ $rp($bill->total_due) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3">
                <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jatuh Tempo</dt>
                <dd class="mt-1 font-semibold">{{ $bill->due_date?->format('d M Y') ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    @if($bill->va_number)
    <div class="rounded-2xl bg-[#0F1E33] p-5 text-white space-y-1">
        <div class="text-xs font-semibold text-blue-300">Virtual Account • berlaku hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }}</div>
        <div class="flex items-center gap-3">
            <div class="text-2xl font-bold tracking-wider flex-1">{{ $bill->va_number }}</div>
            <button type="button" onclick="navigator.clipboard && navigator.clipboard.writeText('{{ $bill->va_number }}')" class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold hover:bg-blue-700">Salin</button>
        </div>
        <div class="text-[11px] text-blue-200">Salin nomor ini bila bayar manual • lunas otomatis via callback Midtrans</div>
    </div>
    @endif
    @if($bill->qris_payload)
    <div class="rounded-2xl bg-[#0F1E33] p-5 text-white space-y-2 text-center">
        <div class="text-xs font-semibold text-blue-300">QRIS • berlaku hingga {{ $bill->expired_at?->format('d M Y H:i') ?? '-' }}</div>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($bill->qris_payload) }}" width="200" height="200" alt="Kode QRIS" class="mx-auto rounded-lg bg-white p-2">
        <div class="text-[11px] text-blue-200">Pindai dengan e-wallet / m-banking • lunas otomatis via callback Midtrans</div>
    </div>
    @endif

    @if(! empty($isArchived))
    <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">Akun arsip: pembayaran dinonaktifkan, hubungi operator.</div>
    @endif

    @if($bill->status !== 'paid' && empty($isArchived))
    @php
        $gw = $gateways ?? ['bca_va' => true, 'bni_va' => true, 'qris' => true];
        $gwLabels = ['bca_va' => 'BCA Virtual Account', 'bni_va' => 'BNI Virtual Account', 'qris' => 'QRIS Dinamis'];
    @endphp
    <div class="ss-card space-y-3">
        <h2 class="text-sm font-bold">Bayar Tagihan</h2>
        @if(! ($gw['bca_va'] ?? false) && ! ($gw['bni_va'] ?? false) && ! ($gw['qris'] ?? false))
        <p class="text-xs text-amber-600 font-semibold">Pembayaran online sedang nonaktif, hubungi bendahara.</p>
        @else
        <div class="flex flex-wrap gap-2">
            <select id="pay-method" class="ss-input !w-auto" aria-label="Metode bayar">
                @foreach($gwLabels as $val => $label)
                    @if(! empty($gw[$val]))<option value="{{ $val }}">{{ $label }}</option>@endif
                @endforeach
            </select>
            <button type="button" id="pay-submit" onclick="submitDetailPay()" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Bayar</button>
            @if($bill->order_id)
            <button type="button" id="pay-check" onclick="checkDetailPay()" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-semibold hover:border-blue-500 hover:text-blue-600">Saya Sudah Bayar (Cek Status)</button>
            @endif
        </div>
        <p id="pay-msg" class="hidden text-xs font-semibold"></p>
        <div id="pay-result" class="hidden rounded-2xl bg-[#0F1E33] p-5 text-white space-y-2 text-center">
            <div class="text-xs font-semibold text-blue-300" id="pay-result-label">Nomor bayar</div>
            <div class="text-2xl font-bold tracking-wider" id="pay-result-va"></div>
            <img id="pay-result-qr" class="hidden mx-auto rounded-lg bg-white p-2" width="200" height="200" alt="Kode QRIS">
            <div class="text-[11px] text-blue-200" id="pay-result-order"></div>
        </div>
        @endif
    </div>
    <script>
    function submitDetailPay() {
        var submit = document.getElementById('pay-submit');
        if (submit.disabled) return;
        submit.disabled = true; submit.textContent = 'Memproses...';
        var method = document.getElementById('pay-method').value;
        fetch('{{ route('portal.tagihan.pay', $bill->id) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ method: method })
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            var msg = document.getElementById('pay-msg');
            msg.classList.remove('hidden');
            if (!res.ok) { msg.textContent = res.body.message || 'Gagal membuat pembayaran.'; msg.className = 'text-xs font-semibold text-rose-600'; submit.disabled = false; submit.textContent = 'Bayar'; return; }
            msg.textContent = 'Order ' + (res.body.transaction.order_id || '-') + ' dibuat. Selesaikan pembayaran sebelum kedaluwarsa.';
            msg.className = 'text-xs font-semibold text-emerald-600';
            var box = document.getElementById('pay-result');
            box.classList.remove('hidden');
            var qrImg = document.getElementById('pay-result-qr');
            if (res.body.transaction.qr_string) {
                document.getElementById('pay-result-label').textContent = 'Pindai QRIS';
                document.getElementById('pay-result-va').textContent = '';
                qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(res.body.transaction.qr_string);
                qrImg.classList.remove('hidden');
            } else {
                document.getElementById('pay-result-label').textContent = 'Virtual Account';
                document.getElementById('pay-result-va').textContent = res.body.transaction.va_number || '-';
                qrImg.classList.add('hidden');
            }
            document.getElementById('pay-result-order').textContent = 'Order ' + (res.body.transaction.order_id || '-');
            setTimeout(function () { location.reload(); }, 15000);
        }).catch(function () { ssAlert('Gagal membuat pembayaran.'); submit.disabled = false; submit.textContent = 'Bayar'; });
    }
    function checkDetailPay() {
        var btn = document.getElementById('pay-check');
        if (btn.disabled) return;
        btn.disabled = true; btn.textContent = 'Mengecek...';
        fetch('{{ route('portal.tagihan.status', $bill->id) }}', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            var msg = document.getElementById('pay-msg');
            msg.classList.remove('hidden');
            if (!res.ok) { msg.textContent = res.body.message || 'Gagal cek status.'; msg.className = 'text-xs font-semibold text-rose-600'; }
            else if (res.body.settled) { msg.textContent = 'Pembayaran lunas. Memuat ulang...'; msg.className = 'text-xs font-semibold text-emerald-600'; setTimeout(function () { location.reload(); }, 1500); return; }
            else { msg.textContent = 'Status: ' + (res.body.status || '-') + '. Selesaikan pembayaran lalu tekan lagi.'; msg.className = 'text-xs font-semibold text-amber-600'; }
            btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)';
        }).catch(function () { ssAlert('Gagal cek status.'); btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)'; });
    }
    </script>
    @endif

    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold">Timeline</h2>
        @forelse($bill->events as $ev)
        <div class="flex gap-2.5 text-xs">
            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
            <div>
                <div class="font-medium">{{ $ev->message ?? $ev->event }}</div>
                <div class="text-[11px] text-slate-400">{{ $ev->created_at?->format('d M Y H:i') }}</div>
            </div>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada event. Pembayaran dan notifikasi WA tercatat di sini.</p>
        @endforelse
    </div>

    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold">Pembayaran</h2>
        @forelse($bill->payments as $pay)
        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3 text-xs">
            <span class="font-bold">{{ $pay->invoice_number }}</span>
            <x-ss-pill status="{{ $pay->status }}" label="{{ $pay->status_label }}" />
            <span class="font-semibold">{{ $rp($pay->amount) }}</span>
            <a href="{{ route('portal.kuitansi', $pay->id) }}" class="text-blue-600 hover:underline font-semibold">Kuitansi</a>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada pembayaran.</p>
        @endforelse
    </div>
@endsection
