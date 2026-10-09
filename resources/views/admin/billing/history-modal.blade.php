{{-- Riwayat Generate NYATA: batch per kategori+periode dari database --}}
<div id="history-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-2xl w-full max-h-[85vh] overflow-y-auto space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Riwayat Billing Generator</h3>
                <p class="text-xs text-slate-400">Batch tagihan dari database</p>
            </div>
            <button onclick="document.getElementById('history-modal').classList.add('hidden')" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px]">
                        <th class="py-2.5 px-3">Batch</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-3">Jumlah</th>
                        <th class="py-2.5 px-3">Total Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($history ?? [] as $h)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                        <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">
                            BAT-{{ $h->period_year }}-{{ str_pad($h->period_month, 2, '0', STR_PAD_LEFT) }}
                            @if($h->academic_year || $h->semester)
                            <div class="text-[10px] font-normal text-slate-400">{{ $h->academic_year ?? '-' }} • {{ $h->semester ? ucfirst($h->semester) : '-' }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-3">{{ $h->feeCategory->name ?? $h->feeCategory->code ?? '-' }}</td>
                        <td class="py-3 px-3">{{ $h->total }} siswa</td>
                        <td class="py-3 px-3 font-semibold">Rp {{ number_format($h->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada batch tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" onclick="document.getElementById('history-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Tutup</button>
        </div>
    </div>
</div>
