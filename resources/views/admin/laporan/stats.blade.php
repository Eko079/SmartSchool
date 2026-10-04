<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Uang masuk sukses">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Penerimaan</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ $stats['total_income'] ?? 0 }}" data-count-fmt="rp">
            Rp {{ number_format($stats['total_income'] ?? 0, 0, ',', '.') }}
        </div>
        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Berdasarkan transaksi berhasil</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Invoice sukses">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Transaksi Berhasil</span>
        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
            <span data-count-up="{{ $stats['total_success'] ?? 0 }}" data-count-fmt="int">{{ $stats['total_success'] ?? 0 }}</span> invoice
        </div>
        <span class="text-[11px] text-slate-400">Dari total {{ $stats['total_transactions'] ?? 0 }} transaksi</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Semua transaksi">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Transaksi</span>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" data-count-up="{{ $stats['total_transactions'] ?? 0 }}" data-count-fmt="int">
            {{ $stats['total_transactions'] ?? 0 }}
        </div>
        <span class="text-[11px] text-slate-400">Tercatat di sistem ERP</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Masuk vs tagihan">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Rasio Kolektibilitas</span>
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" data-count-up="{{ $stats['collection_rate'] ?? 0 }}" data-count-fmt="pct1">
            {{ $stats['collection_rate'] ?? 0 }}%
        </div>
        <span class="text-[11px] text-slate-400">Dari seluruh tagihan aktif</span>
    </div>
</div>
