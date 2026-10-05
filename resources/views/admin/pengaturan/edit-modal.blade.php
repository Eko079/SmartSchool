{{-- Modal Ubah Pengaturan (popup, tanpa edit langsung di halaman) --}}
<div id="settings-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Ubah Pengaturan</h3>
                <p class="text-xs text-slate-400">Profil sekolah, tahun ajaran dan pembayaran</p>
            </div>
            <button type="button" onclick="document.getElementById('settings-modal').classList.add('hidden')" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="settings-form" method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Sekolah</label>
                    <input type="text" name="school_name" value="{{ $settings['school_name'] ?? '' }}" placeholder="Nama sekolah" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NPSN / NSM</label>
                    <input type="text" name="npsn" value="{{ $settings['npsn'] ?? '' }}" placeholder="NPSN" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Resmi</label>
                    <input type="email" name="school_email" value="{{ $settings['school_email'] ?? '' }}" placeholder="email@sekolah.sch.id" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Telepon</label>
                    <input type="tel" name="school_phone" value="{{ $settings['school_phone'] ?? '' }}" placeholder="(021) ..." class="ss-input text-xs !py-2">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                    <input type="text" name="school_address" value="{{ $settings['school_address'] ?? '' }}" placeholder="Alamat sekolah" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran</label>
                    <input type="text" name="academic_year" value="{{ $settings['academic_year'] ?? '' }}" placeholder="2026/2027" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Semester Aktif</label>
                    <select name="active_semester" class="ss-input text-xs !py-2">
                        <option value="Ganjil" {{ ($settings['active_semester'] ?? 'Ganjil') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ ($settings['active_semester'] ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Channel Pembayaran</div>
                @php $gatewayOn = fn($k) => (string) ($settings[$k] ?? '1') === '1'; @endphp
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

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Midtrans Server Key</label>
                    <input type="text" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] ?? '' }}" placeholder="SB-Mid-server-..." class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Midtrans Client Key</label>
                    <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] ?? '' }}" placeholder="SB-Mid-client-..." class="ss-input text-xs !py-2">
                </div>
                <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-white">Mode Sandbox</div>
                        <div class="text-[11px] text-slate-400">Uji transaksi tanpa uang riil</div>
                    </div>
                    <input type="checkbox" name="midtrans_sandbox" value="1" {{ $gatewayOn('midtrans_sandbox') ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-0">
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('settings-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script>
// Klik backdrop menutup modal pengaturan
document.getElementById('settings-modal').addEventListener('click', function (e) {
    if (e.target === this) this.classList.add('hidden');
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.getElementById('settings-modal').classList.add('hidden');
});
</script>
