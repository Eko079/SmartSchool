@php
    use App\Http\Controllers\Admin\DashboardController;

    $maxIncome = max(1, collect($monthlyIncome ?? [])->max('total') ?? 0);
    $circ = round(2 * M_PI * 14, 2); // r=14 → ±87.96
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Bar Chart: pemasukan 12 bulan terakhir (dari tabel payments) --}}
    <div class="ss-card ss-tip lg:col-span-2 flex flex-col justify-between gap-4"
        data-tip="Dihitung dari pembayaran sukses per bulan — arahkan kursor ke tiap batang untuk nominalnya">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Grafik Pembayaran</h2>
                <p class="text-xs text-slate-500">Pemasukan 12 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>Pembayaran</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-200 dark:bg-blue-900"></span>Bulan berjalan</span>
            </div>
        </div>
        @if(($monthlyIncome ?? []) && $maxIncome > 1)
        <div class="flex items-end gap-2 h-44 pt-4 border-b border-slate-200 dark:border-slate-800">
            @foreach($monthlyIncome as $m)
            @php $h = max(4, round($m['total'] / $maxIncome * 100)); @endphp
            <div class="flex-1 flex flex-col items-center gap-2 justify-end h-full group relative">
                <div class="absolute -top-1 hidden group-hover:block rounded-md bg-slate-800 dark:bg-slate-700 px-2 py-1 text-[10px] font-semibold text-white whitespace-nowrap z-10">
                    {{ $m['title'] }} • Rp {{ number_format($m['total'], 0, ',', '.') }}
                </div>
                <div class="ss-bar w-full max-w-[28px] rounded-t-md group-hover:opacity-80 {{ $m['current'] ? 'bg-blue-600' : 'bg-blue-200 dark:bg-blue-900' }}"
                    data-bar-h="{{ $h }}" style="height: {{ $h }}%"></div>
                <span class="text-[11px] {{ $m['current'] ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">{{ $m['label'] }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center gap-1 h-44 text-xs text-slate-400">
            <span class="font-semibold text-slate-500 dark:text-slate-300">Belum ada data pemasukan</span>
            <span>Transaksi sukses akan dirangkum per bulan di sini.</span>
        </div>
        @endif
    </div>

    {{-- Donat: distribusi penerimaan per kategori (dari paid_amount tagihan) --}}
    <div class="ss-card ss-tip flex flex-col justify-between gap-4"
        data-tip="Porsi penerimaan per kategori dari total yang sudah terbayar">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pembayaran per Kategori</h2>
            <p class="text-xs text-slate-500">Distribusi total penerimaan</p>
        </div>
        @if(!empty($categoryShares) && !empty($categoryTop) && ($categoryTop['percent'] ?? 0) > 0)
        @php
            // Segmen presisi: lewati 0% (biang titik oranye), koreksi drift rounding di segmen terakhir.
            $circVal = round($circ, 2);
            $segs = [];
            $acc = 0;
            foreach (($categoryShares ?? []) as $share) {
                if (($share['percent'] ?? 0) <= 0) continue;
                $len = round($share['percent'] / 100 * $circ, 1);
                $segs[] = ['share' => $share, 'len' => $len, 'off' => round($acc, 1)];
                $acc += $len;
            }
            if (!empty($segs)) {
                $drift = round($circ - $acc, 1);
                $segs[count($segs) - 1]['len'] = round($segs[count($segs) - 1]['len'] + $drift, 1);
            }
            $topPct = (float) ($categoryTop['percent'] ?? 0);
        @endphp
        <div class="flex justify-center py-2">
            <div class="relative w-32 h-32">
                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90" role="img"
                    aria-label="Distribusi penerimaan: {{ collect($categoryShares)->map(fn ($s) => $s['code'] . ' ' . number_format($s['percent'], 1, ',', '.') . '%')->implode(', ') }}">
                    <circle cx="18" cy="18" r="14" fill="none" stroke="#e2e8f0" class="dark:stroke-slate-700" stroke-width="4.5"/>
                    @foreach($segs as $seg)
                    <circle cx="18" cy="18" r="14" fill="none" stroke="{{ $seg['share']['stroke'] }}"
                        stroke-width="4.5" stroke-linecap="butt"
                        class="ss-donut-seg" data-len="{{ $seg['len'] }}" data-off="{{ $seg['off'] }}" data-circ="{{ $circVal }}"
                        stroke-dasharray="{{ $seg['len'] }} {{ $circVal }}" stroke-dashoffset="-{{ $seg['off'] }}">
                        <title>{{ $seg['share']['name'] }} ({{ $seg['share']['code'] }}): {{ number_format($seg['share']['percent'], 1, ',', '.') }}% — Rp {{ number_format($seg['share']['amount'], 0, ',', '.') }}</title>
                    </circle>
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl font-bold text-slate-800 dark:text-white" data-donut-count="{{ number_format($topPct, 1, ',', '.') }}" data-donut-decimals="1">{{ number_format($topPct, 1, ',', '.') }}%</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">{{ $categoryTop['code'] }}</span>
                </div>
            </div>
        </div>
        <div class="space-y-2 text-xs border-t border-slate-200 dark:border-slate-800 pt-3">
            @foreach($categoryShares as $share)
            <div class="ss-tip ss-tip-bottom flex items-center justify-between"
                data-tip="{{ $share['name'] }}: Rp {{ number_format($share['amount'], 0, ',', '.') }} dari total penerimaan">
                <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <span class="w-2.5 h-2.5 rounded-full {{ $share['dot'] }}"></span>
                    {{ $share['code'] }}
                </span>
                <span class="font-bold text-slate-800 dark:text-white">{{ number_format($share['percent'], 1, ',', '.') }}%</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center gap-1 py-8 text-xs text-slate-400">
            <span class="font-semibold text-slate-500 dark:text-slate-300">Belum ada penerimaan</span>
            <span>Distribusi kategori muncul setelah ada tagihan terbayar.</span>
        </div>
        @endif
    </div>
</div>
