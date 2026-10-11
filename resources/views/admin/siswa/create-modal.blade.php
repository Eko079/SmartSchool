{{-- Modal Tambah Siswa --}}
<div id="create-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-lg w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Siswa Baru</h3>
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" class="ss-input text-xs !py-2" placeholder="Contoh: 20261013" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                    <input type="text" name="nisn" class="ss-input text-xs !py-2" placeholder="Nomor NISN">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" class="ss-input text-xs !py-2" placeholder="Nama siswa" required>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" id="create-class" class="ss-input text-xs !py-2" required>
                        @foreach($classes ?? [] as $cls)
                            <option value="{{ $cls->id }}" data-grade="{{ $cls->grade }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Masuk <span class="text-slate-400 font-normal" title="Tahun pertama masuk sekolah ini. Menentukan Daftar Ulang vs Seragam">?</span></label>
                    <input type="number" name="entry_year" id="create-entry" min="2000" max="{{ date('Y') + 1 }}" value="{{ (int) (explode('/', $academicYear ?? (date('Y') . '/' . (date('Y') + 1)))[0]) }}" class="ss-input text-xs !py-2">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                    <select name="gender" class="ss-input text-xs !py-2">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status" class="ss-input text-xs !py-2">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="lulus">Lulus</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wali</label>
                    <input type="text" name="guardian_name" class="ss-input text-xs !py-2" placeholder="Ibu / Bapak Wali">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Wali</label>
                    <input type="tel" name="guardian_phone" class="ss-input text-xs !py-2" placeholder="0812-xxxx-xxxx">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="2" class="ss-input text-xs !py-2" placeholder="Jalan, RT/RW, kelurahan, kota..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.querySelector('#create-modal form');
    if (!form) return;
    var taStart = {{ (int) (explode('/', $academicYear ?? (date('Y') . '/' . (date('Y') + 1)))[0]) }};
    form.addEventListener('submit', function (e) {
        var cls = document.getElementById('create-class');
        var grade = cls && cls.selectedOptions[0] ? cls.selectedOptions[0].getAttribute('data-grade') : '';
        var entry = parseInt((document.getElementById('create-entry').value || ''), 10);
        var idx = { X: 0, XI: 1, XII: 2 }[grade];
        if (!isNaN(entry) && idx !== undefined && entry !== (taStart - idx)) {
            e.preventDefault();
            ssConfirm('Tahun masuk ' + entry + ' tidak biasa untuk kelas ' + grade + ' (mis. bukan siswa baru). Tetap simpan?', 'Tetap Simpan').then(function (ok) {
                if (ok) form.submit();
            });
        }
    });
})();
</script>
