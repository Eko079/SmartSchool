{{-- Modal Import Siswa — CSV/Excel, data Excel dibaca mulai A3, preview dulu sebelum masuk DB --}}
<div id="import-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Import Siswa (Preview Dulu)</h3>
            <button onclick="document.getElementById('import-modal').classList.add('hidden')"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">File CSV / Excel <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required
                    class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:font-semibold file:text-blue-600">
                <p class="mt-1.5 text-[11px] leading-relaxed text-slate-400">
                    Kolom: <code>nis, nisn, nama, kelas, gender(L/P), status(aktif/nonaktif/lulus), wali, wa, alamat</code>.<br>
                    Excel dibaca mulai <b>A3</b> ke bawah (baris 1 header, baris 2 contoh). CSV: baris pertama boleh header.<br>
                    Setelah upload tampil preview OK / Duplikat / Error dulu, belum masuk database.
                </p>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Upload & Preview</button>
            </div>
        </form>
    </div>
</div>
