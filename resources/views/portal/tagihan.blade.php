@extends('layouts.portal')
@section('title', 'Tagihan')
@section('page-title', 'Tagihan')

@section('content')
    @php
        $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
        $payOf = fn($b) => $b->payments ? $b->payments->sortByDesc('paid_at')->first() : null;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="ss-card !p-3 space-y-1">
            <div class="text-xs font-medium text-slate-500">Total Menunggu Dibayar</div>
            <div class="text-xl font-bold text-slate-800 dark:text-white" data-count-up="{{ (float) $summary['waiting'] }}" data-count-fmt="rp">{{ $rp($summary['waiting']) }}</div>
            @if((float) ($summary['waiting_fine'] ?? 0) > 0)
            <div class="text-[11px] font-semibold text-rose-500">Termasuk denda {{ $rp($summary['waiting_fine']) }}</div>
            @endif
        </div>
        <div class="ss-card !p-3 space-y-1">
            <div class="text-xs font-medium text-slate-500">Total Sudah Dibayar</div>
            <div class="text-xl font-bold text-emerald-600" data-count-up="{{ (float) $summary['paid'] }}" data-count-fmt="rp">{{ $rp($summary['paid']) }}</div>
        </div>
    </div>

    <div class="ss-card">
        <form method="GET" action="{{ route('portal.tagihan') }}" class="flex flex-wrap items-center gap-2">
            <select name="semester_key" class="ss-input !w-auto min-w-[220px]" onchange="this.form.submit()" aria-label="Pilih Semester">
                <option value="">Pilih Semester</option>
                @foreach(($semesterOptions ?? collect()) as $opt)
                <option value="{{ $opt['key'] }}" {{ ($selKey ?? '') === $opt['key'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                @endforeach
            </select>
            <button type="button" onclick="window.print()" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white hover:bg-emerald-700">Cetak</button>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tagihan, kategori..." class="ss-input !w-auto flex-1 min-w-[200px]">
            <select name="status" class="ss-input !w-auto">
                @foreach(['all' => 'Semua Status', 'unpaid' => 'Belum Bayar', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'overdue' => 'Menunggak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
        </form>
    </div>

    @if(! empty($isArchived))
    <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">Akun arsip: halaman ini hanya riwayat. Pembayaran dinonaktifkan, hubungi operator bila ada tunggakan.</div>
    @endif
    <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Tagihan Semester {{ isset($selSemester) ? strtoupper($selSemester) : '' }} {{ $selYear ?? '' }}</div>
    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">No. Tagihan</th>
            <th class="py-2.5 px-3">No. Urut</th>
            <th class="py-2.5 px-3">Pembayaran</th>
            <th class="py-2.5 px-3">Jumlah Bayar</th>
            <th class="py-2.5 px-3 text-center">Status Bayar</th>
            <th class="py-2.5 px-3">Tanggal Bayar</th>
            <th class="py-2.5 px-3">Chanel Bayar</th>
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>

        @forelse($bills as $i => $bill)
        @php $pay = $payOf($bill); $isPaid = $bill->status === 'paid'; $statusLabel = $bill->status_label; $methodLabel = $pay ? $pay->method_label : '-'; @endphp
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $bill->bill_code }}<div class="text-[10px] font-semibold text-slate-400">{{ $bill->due_info }}</div></td>
            <td class="py-3 px-3 text-slate-400">{{ ($bills->firstItem() ?? 1) + $i }}</td>
            <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $bill->display_name }}</td>
            <td class="py-3 px-3 font-bold whitespace-nowrap">{{ $rp($bill->amount) }}@if((float) $bill->fine_amount > 0)<div class="text-[10px] font-semibold text-rose-500">+ denda {{ $rp($bill->fine_amount) }}</div>@endif</td>
            <td class="py-3 px-3 text-center">
                @if($isPaid)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $statusLabel }}</span>
                @elseif($bill->status === 'partial')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 px-2.5 py-1 text-[10px] font-bold text-blue-700 dark:text-blue-300"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $statusLabel }}</span>
                @elseif($bill->status === 'overdue')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 px-2.5 py-1 text-[10px] font-bold text-rose-700 dark:text-rose-300"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>{{ $statusLabel }}</span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 px-2.5 py-1 text-[10px] font-bold text-amber-700 dark:text-amber-300"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $statusLabel }}</span>
                @endif
            </td>
            <td class="py-3 px-3 whitespace-nowrap">{{ $pay?->paid_at?->format('d M Y') ?? '-' }}</td>
            <td class="py-3 px-3 whitespace-nowrap text-slate-500">{{ $methodLabel }}</td>
            <td class="py-3 px-3 text-right whitespace-nowrap">
                @if(! $isPaid && empty($isArchived))
                <button type="button" onclick="openPayModal({{ $bill->id }}, this)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->display_name }}"
                    data-amount="{{ $rp($bill->total_due) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    class="rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-semibold text-white hover:bg-blue-700">Bayar</button>
                @endif
                <button type="button" onclick="openPayModal({{ $bill->id }}, this, true)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->display_name }}"
                    data-amount="{{ $rp($bill->total_due) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    data-tanggal="{{ $pay?->paid_at?->format('d M Y H:i') ?? '-' }}"
                    data-tempat="{{ $methodLabel }}"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-semibold hover:border-blue-500 hover:text-blue-600">Detail</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="py-6 text-center text-slate-400">Tidak ada tagihan pada TA {{ $selYear ?? '-' }} semester {{ isset($selSemester) ? ucfirst($selSemester) : '-' }}.</td></tr>
        @endforelse

        <x-slot name="pagination">{{ $bills->links() }}</x-slot>
    </x-ss-table>

    <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mt-4">Tagihan Lainnya</div>
    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">No. Tagihan</th>
            <th class="py-2.5 px-3">No. Urut</th>
            <th class="py-2.5 px-3">Pembayaran</th>
            <th class="py-2.5 px-3">Jumlah Bayar</th>
            <th class="py-2.5 px-3 text-center">Status Bayar</th>
            <th class="py-2.5 px-3">Tanggal Bayar</th>
            <th class="py-2.5 px-3">Chanel Bayar</th>
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>

        @forelse(($otherBills ?? collect()) as $j => $bill)
        @php $pay = $payOf($bill); $isPaid = $bill->status === 'paid'; $statusLabel = $bill->status_label; $methodLabel = $pay ? $pay->method_label : '-'; @endphp
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $bill->bill_code }}<div class="text-[10px] font-semibold text-slate-400">{{ $bill->academic_year ?? '-' }} {{ isset($bill->semester) ? ucfirst($bill->semester) : '' }} • {{ $bill->due_info }}</div></td>
            <td class="py-3 px-3 text-slate-400">{{ $j + 1 }}</td>
            <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $bill->display_name }}</td>
            <td class="py-3 px-3 font-bold whitespace-nowrap">{{ $rp($bill->amount) }}@if((float) $bill->fine_amount > 0)<div class="text-[10px] font-semibold text-rose-500">+ denda {{ $rp($bill->fine_amount) }}</div>@endif</td>
            <td class="py-3 px-3 text-center">
                @if($isPaid)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $statusLabel }}</span>
                @elseif($bill->status === 'partial')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 px-2.5 py-1 text-[10px] font-bold text-blue-700 dark:text-blue-300"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $statusLabel }}</span>
                @elseif($bill->status === 'overdue')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 px-2.5 py-1 text-[10px] font-bold text-rose-700 dark:text-rose-300"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>{{ $statusLabel }}</span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 px-2.5 py-1 text-[10px] font-bold text-amber-700 dark:text-amber-300"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $statusLabel }}</span>
                @endif
            </td>
            <td class="py-3 px-3 whitespace-nowrap">{{ $pay?->paid_at?->format('d M Y') ?? '-' }}</td>
            <td class="py-3 px-3 whitespace-nowrap text-slate-500">{{ $methodLabel }}</td>
            <td class="py-3 px-3 text-right whitespace-nowrap">
                @if(! $isPaid && empty($isArchived))
                <button type="button" onclick="openPayModal({{ $bill->id }}, this)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->display_name }}"
                    data-amount="{{ $rp($bill->total_due) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    class="rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-semibold text-white hover:bg-blue-700">Bayar</button>
                @endif
                <button type="button" onclick="openPayModal({{ $bill->id }}, this, true)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->display_name }}"
                    data-amount="{{ $rp($bill->total_due) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    data-tanggal="{{ $pay?->paid_at?->format('d M Y H:i') ?? '-' }}"
                    data-tempat="{{ $methodLabel }}"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-semibold hover:border-blue-500 hover:text-blue-600">Detail</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="py-6 text-center text-slate-400">Tidak Ada Data</td></tr>
        @endforelse

        @if(($otherTotal ?? 0) > ($otherBills ?? collect())->count())
        <x-slot name="footerNote"><div class="text-[11px] text-slate-400">Menampilkan {{ ($otherBills ?? collect())->count() }} dari {{ $otherTotal }} tagihan lainnya.</div></x-slot>
        @endif
    </x-ss-table>

    {{-- Popup detail + pilih metode pembayaran --}}
    <div id="pay-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="ss-card w-full max-w-md space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" id="pm-jenis">-</h3>
                    <p class="text-xs text-slate-400" id="pm-code">-</p>
                </div>
                <button type="button" onclick="closePayModal()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">✕</button>
            </div>
            <dl class="grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2.5"><dt class="text-[10px] font-bold uppercase text-slate-400">Nominal</dt><dd class="font-bold" id="pm-amount">-</dd></div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2.5"><dt class="text-[10px] font-bold uppercase text-slate-400">Jatuh tempo</dt><dd class="font-bold" id="pm-due">-</dd></div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2.5"><dt class="text-[10px] font-bold uppercase text-slate-400">Status</dt><dd class="font-bold" id="pm-status">-</dd></div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2.5"><dt class="text-[10px] font-bold uppercase text-slate-400">Dibayar</dt><dd class="font-bold" id="pm-history">-</dd></div>
            </dl>
            <div id="pm-paybox" class="space-y-2">
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Silakan pilih metode pembayaran</p>
                @php
                    $gw = $gateways ?? ['bca_va' => true, 'bni_va' => true, 'qris' => true];
                    $gwLabels = ['bca_va' => 'BCA VA', 'bni_va' => 'BNI VA', 'qris' => 'QRIS'];
                    $gwFirst = true;
                @endphp
                <div class="grid grid-cols-3 gap-2" id="pm-methods">
                    @foreach($gwLabels as $val => $label)
                        @if(! empty($gw[$val]))
                        <label class="cursor-pointer rounded-xl border border-slate-200 dark:border-slate-700 p-2.5 text-center text-xs font-semibold has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950"><input type="radio" name="pm_method" value="{{ $val }}" class="sr-only" {{ $gwFirst ? 'checked' : '' }}><div>{{ $label }}</div></label>
                        @php $gwFirst = false; @endphp
                        @endif
                    @endforeach
                    @if($gwFirst)
                    <p class="col-span-3 text-xs text-amber-600 font-semibold">Pembayaran online sedang nonaktif, hubungi bendahara.</p>
                    @endif
                </div>
                <p class="hidden text-xs text-emerald-600 font-semibold" id="pm-result"></p>
                <div id="pm-va-box" class="hidden rounded-xl bg-[#0F1E33] p-3 text-white">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Nomor bayar • <span id="pm-expiry"></span></div>
                    <div class="mt-1 flex items-center justify-between gap-2">
                        <span class="text-lg font-bold tracking-wider" id="pm-va"></span>
                        <button type="button" id="pm-copy" onclick="copyPayVa()" class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold hover:bg-blue-700">Salin</button>
                    </div>
                    <img id="pm-qr" class="hidden mx-auto rounded-lg bg-white p-2" style="width:100%;max-width:260px;height:auto" width="260" height="260" alt="Kode QRIS">
                    <div class="text-[11px] text-blue-200" id="pm-order"></div>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-3">
                <button type="button" onclick="closePayModal()" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold">Tutup</button>
                <a href="#" id="pm-detail-link" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold hover:border-blue-500 hover:text-blue-600">Halaman Detail</a>
                <button type="button" id="pm-check" onclick="checkPayModal()" class="hidden rounded-lg border border-emerald-500 px-4 py-2 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950">Saya Sudah Bayar (Cek Status)</button>
                <button type="button" id="pm-submit" onclick="submitPayModal()" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Bayar</button>
            </div>
        </div>
    </div>

    <script>
    var pmBillId = null;
    var pmTimer = null;
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
        var isPaid = g('status') === 'Lunas';
        document.getElementById('pm-paybox').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-submit').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-result').classList.add('hidden');
        document.getElementById('pm-va-box').classList.add('hidden');
        if (pmTimer) { clearInterval(pmTimer); pmTimer = null; }
        var submit = document.getElementById('pm-submit');
        submit.disabled = false; submit.textContent = 'Bayar'; submit.classList.remove('hidden');
        document.getElementById('pm-check').classList.add('hidden');
        document.getElementById('pm-copy').classList.remove('hidden');
        var m = document.getElementById('pay-modal');
        m.classList.remove('hidden'); m.classList.add('flex');
    }
    function closePayModal() {
        var m = document.getElementById('pay-modal');
        m.classList.add('hidden'); m.classList.remove('flex');
        if (pmTimer) { clearInterval(pmTimer); pmTimer = null; }
    }
    function copyPayVa() {
        var t = document.getElementById('pm-va').textContent || '';
        if (navigator.clipboard) { navigator.clipboard.writeText(t); }
    }
    function startPayCountdown(expiredAt) {
        var el = document.getElementById('pm-expiry');
        if (!expiredAt) { el.textContent = ''; return; }
        var end = new Date(expiredAt).getTime();
        if (pmTimer) clearInterval(pmTimer);
        var tick = function () {
            var left = end - Date.now();
            if (left <= 0) { el.textContent = 'kedaluwarsa'; clearInterval(pmTimer); return; }
            var h = Math.floor(left / 3600000), m = Math.floor(left % 3600000 / 60000), s = Math.floor(left % 60000 / 1000);
            el.textContent = 'berlaku ' + h + 'j ' + m + 'm ' + s + 'd';
        };
        tick(); pmTimer = setInterval(tick, 1000);
    }
    function submitPayModal() {
        var submit = document.getElementById('pm-submit');
        if (submit.disabled) return;
        submit.disabled = true; submit.textContent = 'Memproses...';
        var checked = document.querySelector('input[name="pm_method"]:checked');
        var method = checked ? checked.value : 'bca_va';
        fetch('/tagihan/' + pmBillId + '/bayar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ method: method })
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            if (!res.ok) { ssAlert(res.body.message || 'Gagal membuat pembayaran.'); submit.disabled = false; submit.textContent = 'Bayar'; return; }
            var j = res.body;
            var el = document.getElementById('pm-result');
            el.textContent = 'Order: ' + (j.transaction.order_id || '-');
            el.classList.remove('hidden');
            var qrImg = document.getElementById('pm-qr');
            var copyBtn = document.getElementById('pm-copy');
            if (j.transaction.qr_string) {
                document.getElementById('pm-va').textContent = 'Pindai kode QR di bawah';
                qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' + encodeURIComponent(j.transaction.qr_string);
                qrImg.classList.remove('hidden');
                copyBtn.classList.add('hidden');
            } else {
                document.getElementById('pm-va').textContent = j.transaction.va_number || '-';
                qrImg.classList.add('hidden');
                copyBtn.classList.remove('hidden');
            }
            document.getElementById('pm-order').textContent = 'Order ' + (j.transaction.order_id || '-');
            document.getElementById('pm-va-box').classList.remove('hidden');
            startPayCountdown(j.transaction.expired_at);
            submit.classList.add('hidden');
            document.getElementById('pm-check').classList.remove('hidden');
        }).catch(function () { ssAlert('Gagal membuat pembayaran.'); submit.disabled = false; submit.textContent = 'Bayar'; });
    }
    function checkPayModal() {
        var btn = document.getElementById('pm-check');
        if (btn.disabled) return;
        btn.disabled = true; btn.textContent = 'Mengecek...';
        fetch('/tagihan/' + pmBillId + '/status', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); }).then(function (res) {
            if (!res.ok) { ssAlert(res.body.message || 'Gagal cek status.'); }
            else if (res.body.settled) { location.reload(); return; }
            else { ssAlert('Status: ' + (res.body.status || '-') + '. Selesaikan pembayaran lalu tekan lagi.'); }
            btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)';
        }).catch(function () { ssAlert('Gagal cek status.'); btn.disabled = false; btn.textContent = 'Saya Sudah Bayar (Cek Status)'; });
    }
    </script>
@endsection
