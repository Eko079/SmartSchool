{{-- Modal Detail Invoice — diisi dari data-* tombol (tanpa dummy) --}}
<div id="invoice-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-lg w-full space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white" id="inv-modal-id">-</h3>
                <p class="text-xs text-slate-400" id="inv-modal-date">-</p>
            </div>
            <button onclick="closeInvoiceModal()" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400">Status Pembayaran</span>
                <div class="font-bold text-sm text-slate-800 dark:text-white" id="inv-modal-status">-</div>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400">Metode</span>
                <div class="font-semibold text-xs text-slate-700 dark:text-slate-300" id="inv-modal-method">-</div>
            </div>
        </div>

        <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                <span class="text-slate-400">Nama Siswa</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="inv-modal-name">-</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-200 dark:border-slate-800">
                <span class="text-slate-400">Komponen Biaya</span>
                <span class="font-semibold text-slate-800 dark:text-white" id="inv-modal-cat">-</span>
            </div>
            <div class="flex justify-between py-2 text-sm font-bold text-slate-800 dark:text-white">
                <span>Total Dibayar</span>
                <span class="text-blue-600 dark:text-blue-400" id="inv-modal-total">-</span>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" onclick="closeInvoiceModal()" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Tutup</button>
            <button type="button" onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Cetak Kuitansi</button>
        </div>
    </div>
</div>

<script>
function openInvoiceModal(btn) {
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '-') : '-'; };
    document.getElementById('inv-modal-id').textContent = g('inv');
    document.getElementById('inv-modal-date').textContent = 'Dibayar ' + g('date');
    document.getElementById('inv-modal-name').textContent = g('name');
    document.getElementById('inv-modal-cat').textContent = g('cat');
    document.getElementById('inv-modal-total').textContent = g('nom');
    document.getElementById('inv-modal-status').textContent = g('status').charAt(0).toUpperCase() + g('status').slice(1);
    document.getElementById('inv-modal-method').textContent = g('method');
    document.getElementById('invoice-modal').classList.remove('hidden');
}
function closeInvoiceModal() {
    document.getElementById('invoice-modal').classList.add('hidden');
}
document.getElementById('invoice-modal').addEventListener('click', function (e) {
    if (e.target === this) closeInvoiceModal();
});
</script>
