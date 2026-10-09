@php use App\Http\Controllers\Admin\DashboardController; @endphp

<div class="ss-card flex flex-col gap-3">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pembayaran Terbaru</h2>
            <p class="text-xs text-slate-500">6 transaksi lunas terakhir</p>
        </div>
        <a href="{{ route('admin.laporan') }}" title="Buka rekap pembayaran lengkap"
            class="rounded-lg bg-blue-50 dark:bg-blue-950 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900 transition">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    <th class="py-2.5 px-3">No. Invoice</th>
                    <th class="py-2.5 px-3">Siswa / Kategori</th>
                    <th class="py-2.5 px-3 text-center">Metode</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                    <th class="py-2.5 px-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($recentPayments ?? [] as $p)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                    title="Invoice {{ $p->invoice_number }} • {{ $p->student->name ?? 'siswa' }} • Rp {{ number_format($p->amount, 0, ',', '.') }}">
                    <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">{{ $p->invoice_number }}</td>
                    <td class="py-3 px-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">
                                {{ strtoupper(substr($p->student->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-white">{{ $p->student->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $p->bill->feeCategory->name ?? 'Tagihan' }} • {{ $p->student->classRoom->name ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-3 text-center text-slate-500">{{ $p->method_label }}</td>
                    <td class="py-3 px-3 text-center">
                        <span class="pill pill-success">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-right font-bold text-slate-800 dark:text-white">
                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi pembayaran terbaru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3.5 space-y-2 border border-slate-200 dark:border-slate-700">
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
