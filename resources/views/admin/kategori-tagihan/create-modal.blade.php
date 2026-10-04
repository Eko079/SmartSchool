{{-- Modal Tambah Kategori Tagihan --}}
<div id="create-category-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Kategori Tagihan</h3>
            <button onclick="document.getElementById('create-category-modal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kategori-tagihan.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="code" class="ss-input text-xs !py-2 uppercase" placeholder="Contoh: SRG-02" required>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tagihan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" class="ss-input text-xs !py-2" placeholder="Contoh: Seragam Olahraga Tambahan" required>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nominal Default <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                    <input type="number" name="default_amount" class="ss-input text-xs !py-2 !pl-9" placeholder="500000" min="0" required>
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Pembayaran</label>
                <select name="type" class="ss-input text-xs !py-2">
                    <option value="bulanan">Bulanan (SPP/Rutin)</option>
                    <option value="sekali">Sekali Bayar (Insidental/Daftar Ulang)</option>
                    <option value="bebas">Bebas (Dapat dicicil/Gedung)</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                <textarea name="description" rows="2" class="ss-input text-xs !py-2" placeholder="Deskripsi peruntukan biaya..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('create-category-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
