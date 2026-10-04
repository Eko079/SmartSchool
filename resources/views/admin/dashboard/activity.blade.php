@php use App\Http\Controllers\Admin\DashboardController; @endphp

<div class="ss-card ss-tip flex flex-col justify-between gap-4"
    data-tip="Ringkasan kejadian terbaru: pembayaran lunas, tagihan menunggu, dan siswa baru">
    <div class="space-y-3">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Aktivitas Terbaru</h2>
        <div class="space-y-3">
            @forelse($activities ?? [] as $act)
            <div class="flex items-start gap-2.5 text-xs">
                <span class="w-2 h-2 rounded-full {{ $act['dot'] }} mt-1 shrink-0"></span>
                <div class="flex-1">
                    <div class="text-slate-700 dark:text-slate-300 font-medium">{{ $act['message'] }}</div>
                    <div class="text-[10px] text-slate-400">{{ $act['time'] }}</div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400">Belum ada aktivitas. Tagihan & pembayaran terbaru akan muncul di sini.</p>
            @endforelse
        </div>
    </div>

    {{-- Realisasi pemasukan bulan berjalan vs tagihan terbit bulan ini --}}
    <div class="ss-tip ss-tip-bottom rounded-xl bg-slate-50 dark:bg-slate-800 p-3.5 space-y-2 border border-slate-200 dark:border-slate-700"
        data-tip="Pemasukan bulan ini dibagi tagihan yang terbit bulan ini — target ideal 100%">
        <div class="flex items-center justify-between text-xs font-semibold">
            <span class="text-slate-800 dark:text-white">Realisasi Bulan Ini</span>
            <span class="text-blue-600 dark:text-blue-400">{{ $realizationPct ?? 0 }}%</span>
        </div>
        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
            <div class="h-full rounded-full bg-blue-600 transition-all" style="width: {{ min(100, $realizationPct ?? 0) }}%"></div>
        </div>
        <div class="text-[11px] text-slate-500 dark:text-slate-400">
            {{ DashboardController::rpShort((float) ($incomeThisMonth ?? 0)) }}
            dari {{ DashboardController::rpShort((float) ($billedThisMonth ?? 0)) }} tagihan terbit
        </div>
    </div>
</div>
