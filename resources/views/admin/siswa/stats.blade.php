<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Total siswa di database">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Siswa Terdaftar</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ $stats['total'] ?? 0 }}" data-count-fmt="int">{{ $stats['total'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Semua angkatan</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Sedang belajar aktif">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Siswa Aktif</span>
        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" data-count-up="{{ $stats['aktif'] ?? 0 }}" data-count-fmt="int">{{ $stats['aktif'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Status belajar aktif</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Sedang cuti">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Siswa Cuti</span>
        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400" data-count-up="{{ $stats['cuti'] ?? 0 }}" data-count-fmt="int">{{ $stats['cuti'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Perlu konfirmasi berkala</span>
    </div>
    <div class="ss-card ss-tip flex flex-col justify-between gap-2 shadow-xs" data-tip="Sudah lulus">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Alumni / Lulus</span>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" data-count-up="{{ $stats['lulus'] ?? 0 }}" data-count-fmt="int">{{ $stats['lulus'] ?? 0 }}</div>
        <span class="text-[11px] text-slate-400">Telah menyelesaikan studi</span>
    </div>
</div>
