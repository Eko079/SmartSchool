<div class="ss-card flex flex-col justify-between gap-4">
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800">RINGKASAN GENERATE</h2>

        <div class="rounded-xl bg-blue-50 dark:bg-blue-950 p-4 border border-blue-100 dark:border-blue-900">
            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Estimasi</span>
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1" id="sum-total">-</div>
            <div class="text-[11px] text-blue-500 mt-0.5" id="sum-detail">-</div>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Target Siswa</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="sum-target">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Kategori Tagihan</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="sum-cat">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Nominal / Siswa</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="sum-amount">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Jatuh Tempo</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="sum-due">-</span>
            </div>
        </div>

        <div class="flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 p-2.5 text-xs text-emerald-700 dark:text-emerald-300">
            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span id="sum-valid">-</span>
        </div>
    </div>

    <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800">
        <button type="button" onclick="openConfirmModal()" class="ss-btn-primary !py-2.5 text-xs">
            <span id="btn-generate-label">Generate Tagihan</span>
        </button>
        <button type="button" onclick="document.getElementById('billing-form').reset(); billingRecalc();" class="ss-btn-outline !py-2 text-xs">
            Reset Parameter
        </button>
        <p class="text-[10px] text-slate-400 text-center">Cegah duplikat: 1 siswa 1 kategori per periode.</p>
    </div>
</div>
