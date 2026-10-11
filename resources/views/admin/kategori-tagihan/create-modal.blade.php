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
                    <option value="bulanan">Bulanan (SPP/Rutin lintas semester)</option>
                    <option value="semesteran">Semesteran (Paket per semester)</option>
                    <option value="sekali">Sekali Bayar (Insidental/Daftar Ulang)</option>
                    <option value="bebas">Bebas (Dapat dicicil/Gedung)</option>
                </select>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 space-y-3 bg-slate-50/60 dark:bg-slate-800/40">
                <div class="font-bold text-slate-700 dark:text-slate-200">Kapan & Siapa yang Bayar</div>
                <div>
                    <span class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keluar Setiap <span class="text-slate-400 font-normal" title="Dua semester = tagihan dibuat tiap semester">?</span></span>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="semester" value="" checked class="rounded text-blue-600"> Dua semester</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="semester" value="ganjil" class="rounded text-blue-600"> Ganjil saja (Jul–Des)</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="semester" value="genap" class="rounded text-blue-600"> Genap saja (Jan–Jun)</label>
                    </div>
                </div>
                <div>
                    <span class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Untuk Jurusan <span class="text-slate-400 font-normal" title="Jurusan yang wajib membayar tagihan ini">?</span></span>
                    <div class="flex flex-wrap gap-2" data-target-group="majors">
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="majors[]" value="semua" checked class="rounded text-blue-600" onchange="targetGroupChanged(this)"> Semua</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="majors[]" value="IPA" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> IPA</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="majors[]" value="IPS" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> IPS</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="majors[]" value="IBB" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> IBB</label>
                    </div>
                </div>
                <div>
                    <span class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Untuk Kelas <span class="text-slate-400 font-normal" title="Tingkat kelas yang wajib membayar">?</span></span>
                    <div class="flex flex-wrap gap-2" data-target-group="grades">
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="grades[]" value="semua" checked class="rounded text-blue-600" onchange="targetGroupChanged(this)"> Semua</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="grades[]" value="X" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> X</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="grades[]" value="XI" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> XI</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="grades[]" value="XII" class="rounded text-blue-600" onchange="targetGroupChanged(this)"> XII</label>
                    </div>
                </div>
                <div>
                    <span class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Untuk Siswa <span class="text-slate-400 font-normal" title="Baru = tahun masuk sama dengan tahun ajaran berjalan">?</span></span>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="audience" value="semua" checked class="rounded text-blue-600"> Semua siswa</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="audience" value="baru" class="rounded text-blue-600"> Siswa baru saja</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" name="audience" value="lama" class="rounded text-blue-600"> Siswa lama saja</label>
                    </div>
                </div>
                <div>
                    <span class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jatuh Tempo <span class="text-slate-400 font-normal" title="Dikosongkan = 2 minggu setelah tagihan dibuat">?</span></span>
                    <div class="flex gap-2">
                        <select name="due_month" class="ss-input text-xs !py-2">
                            <option value="">Otomatis</option>
                            @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $m => $lbl)
                                <option value="{{ $m }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="due_day" min="1" max="31" placeholder="Tgl" title="Tanggal 1–31" class="ss-input text-xs !py-2 w-20">
                    </div>
                </div>
            </div>
            <script>
            function targetGroupChanged(el) {
                var box = el.closest('[data-target-group]');
                if (!box) return;
                var all = box.querySelector('input[value="semua"]');
                if (el.value === 'semua' && el.checked) {
                    box.querySelectorAll('input:not([value="semua"])').forEach(function (c) { c.checked = false; });
                } else if (el.checked && all) {
                    all.checked = false;
                }
            }
            </script>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                <textarea name="description" rows="2" class="ss-input text-xs !py-2" placeholder="Deskripsi peruntukan biaya..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('create-category-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
