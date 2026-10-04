<div class="space-y-4">
    {{-- Midtrans & Payment Channels --}}
    <div class="ss-card space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pembayaran & Midtrans</h2>
            <p class="text-xs text-slate-400">Channel aktif tampil di portal wali/siswa</p>
        </div>

        <div class="space-y-2 text-xs">
            @php
                $gatewayOn = fn($k) => (string) ($settings[$k] ?? '1') === '1';
            @endphp
            <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 cursor-pointer">
                <span class="font-medium text-slate-700 dark:text-slate-300">BCA Virtual Account</span>
                <input type="checkbox" name="gateway_bca_va" value="1" {{ $gatewayOn('gateway_bca_va') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-0">
            </label>
            <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 cursor-pointer">
                <span class="font-medium text-slate-700 dark:text-slate-300">BNI Virtual Account</span>
                <input type="checkbox" name="gateway_bni_va" value="1" {{ $gatewayOn('gateway_bni_va') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-0">
            </label>
            <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 cursor-pointer">
                <span class="font-medium text-slate-700 dark:text-slate-300">QRIS Dinamis</span>
                <input type="checkbox" name="gateway_qris" value="1" {{ $gatewayOn('gateway_qris') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-0">
            </label>
        </div>

        <div class="space-y-3 pt-3 border-t border-slate-200 dark:border-slate-800 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">MIDTRANS SERVER KEY</label>
                <input type="text" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] ?? '' }}" placeholder="SB-Mid-server-..." class="ss-input text-xs !py-1.5">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">MIDTRANS CLIENT KEY</label>
                <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] ?? '' }}" placeholder="SB-Mid-client-..." class="ss-input text-xs !py-1.5">
            </div>
            <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                <div>
                    <div class="font-semibold text-slate-800 dark:text-white">Mode Sandbox</div>
                    <div class="text-[11px] text-slate-400">Uji transaksi tanpa uang riil</div>
                </div>
                <input type="checkbox" name="midtrans_sandbox" value="1" {{ $gatewayOn('midtrans_sandbox') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-0">
            </label>
        </div>
    </div>

    {{-- Admin & Keamanan (data nyata dari database users) --}}
    <div class="ss-card space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pengguna & Keamanan</h2>
                <p class="text-xs text-slate-400">Staf pengelola sistem</p>
            </div>
        </div>

        <div class="space-y-2 text-xs">
            @forelse($users ?? [] as $u)
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-bold">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white">{{ $u->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $u->email }}</div>
                    </div>
                </div>
                <span class="rounded-md bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2 py-0.5">Admin</span>
            </div>
            @empty
            <p class="text-xs text-slate-400">Belum ada pengguna.</p>
            @endforelse
        </div>
    </div>
</div>
