{{-- Modal Ubah Siswa (popup, tanpa pindah halaman) — isi diisi JS openEditModal() --}}
<div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-lg w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div id="edit-avatar" class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 font-bold">-</div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Ubah Data Siswa</h3>
                    <p class="text-xs text-slate-400" id="edit-sub">-</p>
                </div>
            </div>
            <button onclick="closeEditModal()" title="Tutup"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit-form" method="POST" action="" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" id="edit-nis" class="ss-input text-xs !py-2" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                    <input type="text" name="nisn" id="edit-nisn" class="ss-input text-xs !py-2">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-name" class="ss-input text-xs !py-2" required>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" id="edit-class" class="ss-input text-xs !py-2" required>
                        @foreach($classes ?? [] as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                    <select name="gender" id="edit-gender" class="ss-input text-xs !py-2">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status" id="edit-status" class="ss-input text-xs !py-2">
                        <option value="aktif">Aktif</option>
                        <option value="cuti">Cuti</option>
                        <option value="lulus">Lulus</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wali</label>
                    <input type="text" name="guardian_name" id="edit-wali" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Wali</label>
                    <input type="tel" name="guardian_phone" id="edit-wa" class="ss-input text-xs !py-2">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="address" id="edit-alamat" rows="2" class="ss-input text-xs !py-2"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeEditModal()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, btn) {
    if (!btn || !btn.getAttribute) {
        btn = document.querySelector('button[onclick^="openEditModal(' + id + '"]');
    }
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '') : ''; };
    var name = g('name'), nis = g('nis');

    document.getElementById('edit-avatar').textContent = (name || '?').trim().charAt(0).toUpperCase();
    document.getElementById('edit-sub').textContent = 'NIS: ' + (nis || '-');
    document.getElementById('edit-nis').value = nis;
    document.getElementById('edit-nisn').value = g('nisn');
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-class').value = g('class-id');
    document.getElementById('edit-gender').value = g('gender') || 'L';
    document.getElementById('edit-status').value = g('status') || 'aktif';
    document.getElementById('edit-wali').value = g('wali');
    document.getElementById('edit-wa').value = g('wa');
    document.getElementById('edit-alamat').value = g('alamat');

    document.getElementById('edit-form').action = '{{ url('/admin/siswa') }}/' + (g('id') || id);
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
// Klik backdrop menutup modal edit
document.getElementById('edit-modal').addEventListener('click', function (e) {
    if (e.target === this) closeEditModal();
});
</script>
