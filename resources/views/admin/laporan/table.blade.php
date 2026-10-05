<x-ss-table>
    <x-slot name="filters">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Rincian Riwayat Pembayaran</h2>
                <p class="text-xs text-slate-400">Transaksi kasir dan gateway</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" title="Cetak PDF" onclick="window.print()"
                    class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Export PDF
                </button>
                <button type="button" title="Unduh Excel" id="btn-export-excel"
                    class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Excel
                </button>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.laporan') }}" class="flex flex-wrap items-center justify-between gap-3 pt-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" id="laporan-search" value="{{ request('q') }}" placeholder="Cari invoice / nama..."
                        autocomplete="off" class="ss-input text-xs !py-1.5 !pl-9 w-40 sm:w-52">
                    @foreach(['sort', 'dir', 'method', 'status', 'per_page'] as $k)
                        @if(request()->filled($k))<input type="hidden" name="{{ $k }}" value="{{ request($k) }}">@endif
                    @endforeach
                </div>
                <select name="method" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-1.5 px-2.5 outline-none">
                    <option value="">Semua Metode</option>
                    @foreach($methods ?? [] as $m)
                        <option value="{{ $m }}" {{ request('method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-1.5 px-2.5 outline-none">
                    <option value="">Semua Status</option>
                    @foreach($statuses ?? [] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="per_page" onchange="this.form.submit()" title="Jumlah baris per halaman"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-1.5 px-2.5 outline-none">
                    @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}/hal</option>
                    @endforeach
                </select>
                @if(request()->anyFilled(['q', 'method', 'status']))
                    <a href="{{ route('admin.laporan', request()->only(['sort', 'dir'])) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </x-slot>

    <x-slot name="head">
        <x-ss-th sort="invoice" label="No. Invoice" baseUrl="{{ route('admin.laporan') }}" />
        <x-ss-th sort="student" label="Siswa" baseUrl="{{ route('admin.laporan') }}" />
        <th class="py-2.5 px-3">Kategori</th>
        <x-ss-th sort="amount" label="Nominal" baseUrl="{{ route('admin.laporan') }}" />
        <x-ss-th sort="date" label="Tgl Bayar" baseUrl="{{ route('admin.laporan') }}" />
        <th class="py-2.5 px-3">Metode</th>
        <x-ss-th sort="status" label="Status" align="center" baseUrl="{{ route('admin.laporan') }}" />
        <th class="py-2.5 px-3 text-right">Aksi</th>
    </x-slot>

    @forelse($payments ?? [] as $in)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">{{ $in->invoice_number }}</td>
        <td class="py-3 px-3">
            <div class="font-medium text-slate-800 dark:text-white">{{ $in->student->name ?? '-' }}</div>
            <div class="text-[11px] text-slate-400">NIS: {{ $in->student->nis ?? '-' }} • {{ $in->student->classRoom->name ?? '-' }}</div>
        </td>
        <td class="py-3 px-3 font-medium">{{ $in->bill->feeCategory->name ?? '-' }}</td>
        <td class="py-3 px-3 font-bold text-slate-700 dark:text-slate-300">
            Rp {{ number_format($in->amount, 0, ',', '.') }}
        </td>
        <td class="py-3 px-3 text-slate-500">{{ $in->paid_at ? $in->paid_at->format('d M Y H:i') : '-' }}</td>
        <td class="py-3 px-3 text-slate-500">{{ $in->payment_method }}</td>
        <td class="py-3 px-3 text-center">
            <x-ss-pill :status="$in->status" />
        </td>
        <td class="py-3 px-3 text-right">
            <button type="button" title="Detail {{ $in->invoice_number }}"
                onclick="openInvoiceModal(this)"
                data-inv="{{ $in->invoice_number }}"
                data-name="{{ $in->student->name ?? '-' }}"
                data-cat="{{ $in->bill->feeCategory->name ?? '-' }}"
                data-nom="Rp {{ number_format($in->amount, 0, ',', '.') }}"
                data-status="{{ $in->status }}"
                data-method="{{ $in->payment_method }}"
                data-date="{{ $in->paid_at ? $in->paid_at->format('d M Y H:i') : '-' }}"
                class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:bg-slate-50">Detail</button>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="py-6 text-center text-slate-400">
            @if(request()->anyFilled(['q', 'method', 'status']))
                Tidak cocok dengan filter. <a href="{{ route('admin.laporan') }}" class="text-blue-600 hover:underline">Tampilkan semua</a>.
            @else
                Belum ada riwayat transaksi.
            @endif
        </td>
    </tr>
    @endforelse

    @if(isset($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
    <x-slot name="pagination">
        {{ $payments->links() }}
    </x-slot>
    @endif
</x-ss-table>

{{-- Live search khusus tabel ini --}}
<script>
(function () {
    var input = document.getElementById('laporan-search');
    if (!input) return;
    var card = input.closest('.ss-card');
    var tbody = card ? card.querySelector('tbody') : (document.getElementById('ss-table-body') || null);
    var pager = card ? card.querySelector('#ss-pagination') : null;
    var timer = null;

    function applyFilter() {
        if (!tbody) return;
        var q = input.value.trim().toLowerCase();
        var filtering = q !== '';
        var visible = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            if (tr.querySelector('td[colspan]')) { tr.style.display = 'none'; return; }
            var hit = !filtering || (tr.innerText || '').toLowerCase().indexOf(q) !== -1;
            tr.style.display = hit ? '' : 'none';
            if (hit) visible++;
        });
        if (pager) pager.style.display = filtering ? 'none' : '';
        var old = tbody.querySelector('[data-live-empty]');
        if (old) old.remove();
        if (visible === 0 && filtering) {
            var tr = document.createElement('tr');
            tr.setAttribute('data-live-empty', '1');
            tr.innerHTML = '<td colspan="8" class="py-6 text-center text-slate-400">Tidak ada hasil untuk pencarian ini.</td>';
            tbody.appendChild(tr);
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(applyFilter, 120);
    });
    input.closest('form').addEventListener('submit', function (e) { e.preventDefault(); applyFilter(); });
})();
</script>
