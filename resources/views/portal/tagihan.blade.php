@extends('layouts.portal')
@section('title', 'Tagihan')
@section('page-title', 'Tagihan')

@section('content')
    @php
        $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
        $payOf = fn($b) => $b->payments ? $b->payments->sortByDesc('paid_at')->first() : null;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="ss-card !p-3">
            <div class="text-xs font-medium text-slate-500">Total Menunggu Dibayar</div>
            <div class="text-xl font-bold text-slate-800 dark:text-white" data-count-up="{{ (float) $summary['waiting'] }}" data-count-fmt="rp">{{ $rp($summary['waiting']) }}</div>
        </div>
        <div class="ss-card !p-3">
            <div class="text-xs font-medium text-slate-500">Total Sudah Dibayar</div>
            <div class="text-xl font-bold text-emerald-600" data-count-up="{{ (float) $summary['paid'] }}" data-count-fmt="rp">{{ $rp($summary['paid']) }}</div>
        </div>
    </div>

    <div class="ss-card">
        <form method="GET" action="{{ route('portal.tagihan') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tagihan, kategori..." class="ss-input !w-auto flex-1 min-w-[200px]">
            <select name="academic_year" class="ss-input !w-auto" onchange="this.form.submit()">
                @foreach(($academicYears ?? collect()) as $ta)
                <option value="{{ $ta }}" {{ ($selYear ?? request('academic_year')) === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            <select name="semester" class="ss-input !w-auto" onchange="this.form.submit()">
                <option value="ganjil" {{ ($selSemester ?? 'ganjil') === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                <option value="genap" {{ ($selSemester ?? '') === 'genap' ? 'selected' : '' }}>Semester Genap</option>
            </select>
            <select name="status" class="ss-input !w-auto">
                @foreach(['all' => 'Semua Status', 'unpaid' => 'Belum Bayar', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'overdue' => 'Menunggak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
        </form>
    </div>

    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">No</th>
            <th class="py-2.5 px-3">Kode</th>
            <th class="py-2.5 px-3">Kategori</th>
            <th class="py-2.5 px-3">Nominal</th>
            <th class="py-2.5 px-3 text-center">Status</th>
            <th class="py-2.5 px-3">Pembayaran</th>
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>

        @forelse($bills as $i => $bill)
        @php $pay = $payOf($bill); $isPaid = $bill->status === 'paid'; $statusLabel = $bill->status_label; $methodLabel = $pay ? $pay->method_label : '-'; @endphp
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 text-slate-400">{{ ($bills->firstItem() ?? 1) + $i }}</td>
            <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $bill->bill_code }}</td>
            <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $bill->feeCategory->name ?? '-' }}</td>
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
            <td class="py-3 px-3 whitespace-nowrap">{{ $pay?->paid_at?->format('d M Y') ?? '-' }}<div class="text-[10px] font-medium text-slate-400">{{ $methodLabel }}</div></td>
            <td class="py-3 px-3 text-right whitespace-nowrap">
                @if(! $isPaid)
                <button type="button" onclick="openPayModal({{ $bill->id }}, this)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->feeCategory->name ?? '-' }}"
                    data-amount="{{ $rp($bill->amount) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    class="rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-semibold text-white hover:bg-blue-700">Bayar</button>
                @endif
                <button type="button" onclick="openPayModal({{ $bill->id }}, this, true)"
                    data-code="{{ $bill->bill_code }}"
                    data-jenis="{{ $bill->feeCategory->name ?? '-' }}"
                    data-amount="{{ $rp($bill->amount) }}"
                    data-due="{{ $bill->due_date?->format('d M Y') ?? '-' }}"
                    data-status="{{ $statusLabel }}"
                    data-tanggal="{{ $pay?->paid_at?->format('d M Y H:i') ?? '-' }}"
                    data-tempat="{{ $methodLabel }}"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-semibold hover:border-blue-500 hover:text-blue-600">Detail</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="py-6 text-center text-slate-400">Tidak ada tagihan pada TA {{ $selYear ?? '-' }} semester {{ isset($selSemester) ? ucfirst($selSemester) : '-' }}.</td></tr>
        @endforelse

        <x-slot name="pagination">{{ $bills->links() }}</x-slot>
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
                <div class="grid grid-cols-3 gap-2" id="pm-methods">
                    <label class="cursor-pointer rounded-xl border border-slate-200 dark:border-slate-700 p-2.5 text-center text-xs font-semibold has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950"><input type="radio" name="pm_method" value="bca_va" class="sr-only" checked><div>BCA VA</div></label>
                    <label class="cursor-pointer rounded-xl border border-slate-200 dark:border-slate-700 p-2.5 text-center text-xs font-semibold has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950"><input type="radio" name="pm_method" value="bni_va" class="sr-only"><div>BNI VA</div></label>
                    <label class="cursor-pointer rounded-xl border border-slate-200 dark:border-slate-700 p-2.5 text-center text-xs font-semibold has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950"><input type="radio" name="pm_method" value="qris" class="sr-only"><div>QRIS</div></label>
                </div>
                <p class="hidden text-xs text-emerald-600 font-semibold" id="pm-result"></p>
                <div id="pm-va-box" class="hidden rounded-xl bg-[#0F1E33] p-3 text-white">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Nomor bayar • <span id="pm-expiry"></span></div>
                    <div class="mt-1 flex items-center justify-between gap-2">
                        <span class="text-lg font-bold tracking-wider" id="pm-va"></span>
                        <button type="button" onclick="copyPayVa()" class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold hover:bg-blue-700">Salin</button>
                    </div>
                    <div class="text-[11px] text-blue-200" id="pm-order"></div>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-3">
                <button type="button" onclick="closePayModal()" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold">Tutup</button>
                <a href="#" id="pm-detail-link" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold hover:border-blue-500 hover:text-blue-600">Halaman Detail</a>
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
        var isPaid = g('status') === 'LUNAS';
        document.getElementById('pm-paybox').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-submit').style.display = (detailOnly || isPaid) ? 'none' : '';
        document.getElementById('pm-result').classList.add('hidden');
        document.getElementById('pm-va-box').classList.add('hidden');
        if (pmTimer) { clearInterval(pmTimer); pmTimer = null; }
        var submit = document.getElementById('pm-submit');
        submit.disabled = false; submit.textContent = 'Bayar';
        var m = document.getElementById('pay-modal');
        m.classList.remove('hidden'); m.classList.add('flex');
    }
    function closePayModal() {
        var m = document.getElementById('pay-modal');
        m.classList.add('hidden'); m.classList.remove('flex');
        if (pmTimer) { clearInterval(pmTimer); pmTimer = null; }
    }
    document.getElementById('pay-modal').addEventListener('click', function (e) { if (e.target === this) closePayModal(); });
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
        }).then(function (r) { return r.json(); }).then(function (j) {
            var el = document.getElementById('pm-result');
            el.textContent = 'Order: ' + (j.transaction.order_id || '-');
            el.classList.remove('hidden');
            document.getElementById('pm-va').textContent = j.transaction.va_number || '-';
            document.getElementById('pm-order').textContent = 'Order ' + (j.transaction.order_id || '-');
            document.getElementById('pm-va-box').classList.remove('hidden');
            startPayCountdown(j.transaction.expired_at);
            setTimeout(function () { location.reload(); }, 8000);
        }).catch(function () { alert('Gagal membuat pembayaran.'); submit.disabled = false; submit.textContent = 'Bayar'; });
    }
    </script>
@endsection
