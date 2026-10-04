<div class="ss-card !p-4">
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
