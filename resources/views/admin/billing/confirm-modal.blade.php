{{-- Modal Konfirmasi Generate — angka diisi JS dari form (tanpa dummy) --}}
<div id="confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Konfirmasi Pembuatan Billing</h3>
            <button onclick="closeConfirmModal()" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
            <p>Tagihan massal dengan rincian:</p>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-1.5">
                <div class="flex justify-between"><span>Kategori:</span><strong class="text-slate-800 dark:text-white" id="cf-cat">-</strong></div>
                <div class="flex justify-between"><span>Jumlah Siswa:</span><strong class="text-slate-800 dark:text-white" id="cf-count">-</strong></div>
                <div class="flex justify-between"><span>Total Estimasi:</span><strong class="text-blue-600 dark:text-blue-400" id="cf-total">-</strong></div>
                <div class="flex justify-between"><span>Jatuh Tempo:</span><strong class="text-slate-800 dark:text-white" id="cf-due">-</strong></div>
            </div>
            <p class="text-[11px] text-amber-600 dark:text-amber-400">Duplikat (siswa + kategori + periode sama) dilewati otomatis.</p>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" onclick="closeConfirmModal()" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
            <button type="button" onclick="document.getElementById('billing-form').submit()" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Ya, Generate Tagihan</button>
        </div>
    </div>
</div>

<script>
function openConfirmModal() {
    var count = (typeof billingCheckedCount === 'function') ? billingCheckedCount() : 0;
    if (count === 0) {
        alert('Pilih minimal 1 kelas target dulu.');
        return;
    }
    var cat = (typeof billingSelectedCategory === 'function') ? billingSelectedCategory() : { code: '-', name: '-' };
    var amount = parseInt(document.getElementById('input-amount').value || '0', 10);
    var month = document.getElementById('bill-month').selectedOptions[0].text;
    var year = document.getElementById('bill-year').value;
    var due = document.getElementById('bill-due').value || '-';

    document.getElementById('cf-cat').textContent = (cat.code || '') + ' ' + month + ' ' + year;
    document.getElementById('cf-count').textContent = count + ' Siswa';
    document.getElementById('cf-total').textContent = (typeof fmtRp === 'function') ? fmtRp(amount * count) : (amount * count);
    document.getElementById('cf-due').textContent = due;
    document.getElementById('confirm-modal').classList.remove('hidden');
}
function closeConfirmModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
}
document.getElementById('confirm-modal').addEventListener('click', function (e) {
    if (e.target === this) closeConfirmModal();
});
</script>
