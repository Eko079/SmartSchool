@php
    use App\Http\Controllers\Admin\DashboardController;

    $cards = [
        [
            'title' => 'Total Terkumpul',
            'val' => 'Rp ' . number_format($totalIncome ?? 0, 0, ',', '.'),
            'count' => (float) ($totalIncome ?? 0),
            'fmt' => 'rp',
            'bg' => 'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400',
            'type' => 'curr',
            'tip' => 'Akumulasi seluruh pembayaran berstatus sukses'
                . (isset($incomeThisMonth) ? ' • bulan ini ' . DashboardController::rpShort((float) $incomeThisMonth) : ''),
        ],
        [
            'title' => 'Tagihan Tertunggak',
            'val' => 'Rp ' . number_format($totalPending ?? 0, 0, ',', '.'),
            'count' => (float) ($totalPending ?? 0),
            'fmt' => 'rp',
            'bg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-400',
            'type' => 'bill',
            'tip' => 'Selisih total tagihan terbit dikurangi yang sudah terbayar',
        ],
        [
            'title' => 'Siswa Aktif',
            'val' => number_format($activeStudents ?? 0, 0, ',', '.'),
            'count' => (float) ($activeStudents ?? 0),
            'fmt' => 'int',
            'bg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400',
            'type' => 'user',
            'tip' => 'Siswa berstatus aktif dari total ' . number_format($totalStudents ?? 0, 0, ',', '.') . ' siswa terdaftar',
        ],
        [
            'title' => 'Rasio Pelunasan',
            'val' => ($collectionRate ?? 0) . '%',
            'count' => (float) ($collectionRate ?? 0),
            'fmt' => 'pct1',
            'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400',
            'type' => 'check',
            'tip' => 'Total terbayar dibagi total tagihan terbit, dalam persen',
        ],
    ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach($cards as $i => $s)
    <div class="ss-card ss-tip flex flex-col justify-between gap-3 shadow-xs" data-tip="{{ $s['tip'] ?? '' }}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $s['title'] }}</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $s['bg'] }}">
                @if($s['type'] === 'curr')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v12M15 9.5a2.5 2.5 0 00-5 0c0 4 5 1.5 5 5a2.5 2.5 0 01-5 0"/></svg>
                @elseif($s['type'] === 'bill')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 14l6-6M9 8h.01M15 14h.01M4 4h16v16H4z"/></svg>
                @elseif($s['type'] === 'user')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ $s['count'] ?? 0 }}" data-count-fmt="{{ $s['fmt'] ?? 'int' }}">{{ $s['val'] }}</div>
            <div class="flex items-center gap-1.5 text-xs mt-1">
                @if($i === 0)
                    @if(!empty($incomeDelta))
                        <span class="{{ ($incomeDeltaUp ?? true) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} font-semibold">{{ $incomeDelta }}</span>
                        <span class="text-slate-400">vs bulan lalu</span>
                    @else
                        <span class="text-slate-400">bulan ini {{ DashboardController::rpShort((float) ($incomeThisMonth ?? 0)) }}</span>
                    @endif
                @elseif($i === 1)
                    @if(($unpaidCount ?? 0) + ($overdueCount ?? 0) > 0)
                        <span class="text-slate-400">{{ $unpaidCount }} belum bayar • {{ $overdueCount }} menunggak</span>
                    @else
                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Tidak ada tunggakan</span>
                    @endif
                @elseif($i === 2)
                    <span class="text-slate-400">dari {{ number_format($totalStudents ?? 0, 0, ',', '.') }} total siswa</span>
                @else
                    <span class="text-slate-400">{{ DashboardController::rpShort((float) ($totalPaid ?? 0)) }} dari {{ DashboardController::rpShort((float) ($totalBilled ?? 0)) }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
