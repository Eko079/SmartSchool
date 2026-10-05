<div class="ss-card !p-4 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Alur Generate Tagihan</h2>
            <p class="text-xs text-slate-400">Ikuti 3 langkah sampai review</p>
        </div>
        <button type="button" title="Riwayat generate" onclick="document.getElementById('history-modal').classList.remove('hidden')"
            class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Riwayat Generate
        </button>
    </div>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Step 1 --}}
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-xs">1</div>
            <div>
                <div class="text-xs font-bold text-slate-800 dark:text-white">Kategori & Periode</div>
                <div class="text-[11px] text-slate-400">Pilih jenis tagihan</div>
            </div>
        </div>
        <div class="hidden sm:block flex-1 h-[2px] bg-slate-200 dark:bg-slate-700"></div>

        {{-- Step 2 --}}
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-xs">2</div>
            <div>
                <div class="text-xs font-bold text-slate-800 dark:text-white">Target Siswa</div>
                <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold"><span id="step-target-count">{{ $stats['total_students'] ?? 0 }}</span> siswa terpilih</div>
            </div>
        </div>
        <div class="hidden sm:block flex-1 h-[2px] bg-slate-200 dark:bg-slate-700"></div>

        {{-- Step 3 --}}
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs">3</div>
            <div>
                <div class="text-xs font-bold text-slate-800 dark:text-white">Review & Generate</div>
                <div class="text-[11px] text-slate-400">Cek duplikat otomatis</div>
            </div>
        </div>
    </div>
</div>
