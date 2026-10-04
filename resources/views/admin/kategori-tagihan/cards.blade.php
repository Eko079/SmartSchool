<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Total kategori">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Kategori</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ $stats['total_categories'] ?? 0 }}" data-count-fmt="int">{{ $stats['total_categories'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Semua tipe tagihan</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Siap dipakai billing">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Kategori Aktif</span>
        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" data-count-up="{{ $stats['active_categories'] ?? 0 }}" data-count-fmt="int">{{ $stats['active_categories'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Tampil di generator</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Invoice terbit">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Tagihan</span>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" data-count-up="{{ $stats['total_bills'] ?? 0 }}" data-count-fmt="int">{{ $stats['total_bills'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Dari semua kategori</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Uang sudah masuk">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Terkumpul</span>
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" data-count-up="{{ $stats['total_revenue'] ?? 0 }}" data-count-fmt="rp">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
        <span class="text-[11px] text-slate-400">Total paid_amount</span>
    </div>
</div>
