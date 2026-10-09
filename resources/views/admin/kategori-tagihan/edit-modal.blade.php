{{-- Modal Ubah Kategori (popup, tanpa pindah halaman) --}}
<div id="edit-category-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Ubah Kategori Tagihan</h3>
                <p class="text-xs text-slate-400" id="edit-cat-sub">-</p>
            </div>
            <button onclick="closeCategoryEdit()" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit-cat-form" method="POST" action="" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="code" id="edit-cat-code" class="ss-input text-xs !py-2 uppercase" required>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tagihan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-cat-name" class="ss-input text-xs !py-2" required>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nominal Default <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                    <input type="number" name="default_amount" id="edit-cat-amount" class="ss-input text-xs !py-2 !pl-9" min="0" required>
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Pembayaran</label>
                <select name="type" id="edit-cat-type" class="ss-input text-xs !py-2">
                    <option value="bulanan">Bulanan (SPP/Rutin lintas semester)</option>
                    <option value="semesteran">Semesteran (Paket per semester)</option>
                    <option value="sekali">Sekali Bayar (Insidental/Daftar Ulang)</option>
                    <option value="bebas">Bebas (Dapat dicicil/Gedung)</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                <textarea name="description" id="edit-cat-desc" rows="2" class="ss-input text-xs !py-2"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeCategoryEdit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCategoryEdit(id, btn) {
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '') : ''; };
    document.getElementById('edit-cat-sub').textContent = g('code') + ' • ' + g('name');
    document.getElementById('edit-cat-code').value = g('code');
    document.getElementById('edit-cat-name').value = g('name');
    document.getElementById('edit-cat-amount').value = g('amount');
    document.getElementById('edit-cat-type').value = g('type') || 'bulanan';
    document.getElementById('edit-cat-desc').value = g('desc');
    document.getElementById('edit-cat-form').action = '/admin/kategori-tagihan/' + (g('id') || id);
    document.getElementById('edit-category-modal').classList.remove('hidden');
}
function closeCategoryEdit() {
    document.getElementById('edit-category-modal').classList.add('hidden');
}
document.getElementById('edit-category-modal').addEventListener('click', function (e) {
    if (e.target === this) closeCategoryEdit();
});
</script>
