<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Tren penerimaan 12 bulan nyata --}}
    <div class="ss-card ss-tip lg:col-span-2 flex flex-col justify-between gap-4" data-tip="Sukses 12 bulan">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Tren Penerimaan • {{ $labels[0] ?? '' }} – {{ $labels[11] ?? '' }}</h2>
            <p class="text-xs text-slate-500">Total pembayaran sukses per bulan</p>
        </div>
        @php
            $max = max(max($trend ?? [0]), 1);
            $lastIdx = count($trend ?? []) - 1;
        @endphp
        <div class="flex items-end gap-2 h-44 pt-4 border-b border-slate-200 dark:border-slate-800">
            @foreach($trend ?? [] as $i => $val)
            @php $h = max(round($val / $max * 100), $val > 0 ? 4 : 1); @endphp
            <div class="flex-1 flex flex-col items-center gap-2 justify-end h-full group" title="{{ $labels[$i] ?? '' }}: Rp {{ number_format($val, 0, ',', '.') }}">
                <div class="ss-bar w-full max-w-[28px] rounded-t-md group-hover:opacity-80 {{ $i === $lastIdx ? 'bg-blue-600' : 'bg-blue-200 dark:bg-blue-900' }}" data-bar-h="{{ $h }}" style="height: {{ $h }}%"></div>
                <span class="text-[11px] {{ $i === $lastIdx ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">{{ $labels[$i] ?? '' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Donat status tagihan nyata --}}
    @php
        $paid = $donut['paid'] ?? 0;
        $wait = $donut['waiting'] ?? 0;
        $late = $donut['overdue'] ?? 0;
        $tot = max($donutTotal ?? ($paid + $wait + $late), 1);
        $c = round(2 * M_PI * 14, 2); // keliling lingkaran r=14 → ±87.96
        // Segmen presisi: lewati 0 (biang titik kaku), koreksi drift rounding di segmen terakhir.
        $rawSegs = [
            ['color' => '#22c55e', 'count' => $paid, 'tip' => 'Lunas • ' . $paid . ' tagihan • ' . round($paid / $tot * 100) . '%'],
            ['color' => '#f59e0b', 'count' => $wait, 'tip' => 'Menunggu • ' . $wait . ' tagihan • ' . round($wait / $tot * 100) . '%'],
            ['color' => '#ef4444', 'count' => $late, 'tip' => 'Tertunggak • ' . $late . ' tagihan • ' . round($late / $tot * 100) . '%'],
        ];
        $segs = [];
        $acc = 0;
        foreach ($rawSegs as $rs) {
            if ($rs['count'] <= 0) continue;
            $len = round($rs['count'] / $tot * $c, 1);
            $segs[] = ['color' => $rs['color'], 'tip' => $rs['tip'], 'len' => $len, 'off' => round($acc, 1)];
            $acc += $len;
        }
        if (!empty($segs)) {
            $drift = round($c - $acc, 1);
            $segs[count($segs) - 1]['len'] = round($segs[count($segs) - 1]['len'] + $drift, 1);
        }
        $pctPaid = round($paid / $tot * 100);
    @endphp
    <div class="ss-card ss-tip flex flex-col justify-between gap-4" data-tip="Porsi status">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Komposisi Status</h2>
            <p class="text-xs text-slate-500">Seluruh tagihan di database</p>
        </div>
        <div class="flex justify-center py-2">
            <div class="relative w-32 h-32">
                <div data-donut-tip class="absolute left-1/2 -translate-x-1/2 -top-1 hidden rounded-md bg-slate-800 dark:bg-slate-700 px-2 py-1 text-[10px] font-semibold text-white whitespace-nowrap z-10 pointer-events-none"></div>
                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                    <circle cx="18" cy="18" r="14" fill="none" stroke="#e2e8f0" class="dark:stroke-slate-700" stroke-width="4.5"/>
                    @foreach($segs as $seg)
                    <circle cx="18" cy="18" r="14" fill="none" stroke="{{ $seg['color'] }}" stroke-width="4.5" stroke-linecap="butt"
                        class="ss-donut-seg" data-len="{{ $seg['len'] }}" data-off="{{ $seg['off'] }}" data-circ="{{ $c }}"
                        data-seg-tip="{{ $seg['tip'] }}"
                        stroke-dasharray="{{ $seg['len'] }} {{ $c }}" stroke-dashoffset="-{{ $seg['off'] }}"
                        style="pointer-events: stroke; cursor: pointer" />
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl font-bold text-slate-800 dark:text-white" data-donut-count="{{ $pctPaid }}" data-donut-decimals="0">{{ $pctPaid }}%</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Lunas</span>
                </div>
            </div>
        </div>
        <div class="space-y-2 text-xs border-t border-slate-200 dark:border-slate-800 pt-3">
            <div class="flex items-center justify-between" title="Lunas • {{ $paid }} tagihan">
                <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Lunas • {{ $paid }}</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ round($paid / $tot * 100) }}%</span>
            </div>
            <div class="flex items-center justify-between" title="Menunggu • {{ $wait }} tagihan">
                <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Menunggu • {{ $wait }}</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ round($wait / $tot * 100) }}%</span>
            </div>
            <div class="flex items-center justify-between" title="Tertunggak • {{ $late }} tagihan">
                <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Tertunggak • {{ $late }}</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ round($late / $tot * 100) }}%</span>
            </div>
        </div>
    </div>
</div>
