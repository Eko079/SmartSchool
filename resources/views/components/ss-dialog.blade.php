{{-- ============================================================
  Popup peringatan/konfirmasi global SmartSchool.
  Pengganti alert()/confirm() bawaan browser agar tampil
  se-style web (terang + gelap).

  Cara pakai (di JS halaman mana pun):
    ssAlert('Centang dulu siswanya.');                    // 1 tombol Oke
    ssConfirm('Hapus data ini?', 'Hapus').then(function (ok) {
        if (!ok) return;
        ... lanjut aksi ...
    });
    // Untuk onsubmit form:
    onsubmit="return ssConfirmSubmit(this, 'Yakin hapus?', 'Hapus')"

  Pesan multi-baris (\n) tampil apa adanya. Teks diset via
  textContent (aman dari XSS).
------------------------------------------------------------ --}}
<div id="ss-dialog" class="fixed inset-0 hidden items-center justify-center bg-black/50 p-4" style="z-index:70">
    <div class="ss-card w-full max-w-sm shadow-xl">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200 dark:border-slate-800">
            <span id="ss-dialog-icon" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-500 dark:bg-amber-950 dark:text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </span>
            <h3 id="ss-dialog-title" class="text-sm font-bold text-slate-800 dark:text-white">Perhatian</h3>
        </div>
        <p id="ss-dialog-msg" class="py-3 text-xs leading-relaxed text-slate-600 dark:text-slate-300" style="white-space:pre-line"></p>
        <div class="flex justify-end gap-2">
            <button type="button" id="ss-dialog-cancel" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
            <button type="button" id="ss-dialog-ok" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition">Oke</button>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.ssAlert) return; // sudah dimuat (cegah definisi ganda)
    var box = document.getElementById('ss-dialog');
    var titleEl = document.getElementById('ss-dialog-title');
    var msgEl = document.getElementById('ss-dialog-msg');
    var okBtn = document.getElementById('ss-dialog-ok');
    var cancelBtn = document.getElementById('ss-dialog-cancel');
    if (!box || !okBtn) return;
    var queue = [];
    var busy = false;
    var current = null;

    function open() {
        if (busy) return;
        var job = queue.shift();
        if (!job) return;
        busy = true;
        current = job;
        titleEl.textContent = job.title;
        msgEl.textContent = job.message;
        okBtn.textContent = job.okLabel;
        cancelBtn.style.display = job.mode === 'confirm' ? '' : 'none';
        box.classList.remove('hidden');
        box.classList.add('flex');
        try { okBtn.focus(); } catch (e) {}
    }
    function close(result) {
        if (!busy) return;
        box.classList.add('hidden');
        box.classList.remove('flex');
        busy = false;
        var job = current;
        current = null;
        if (job) job.resolve(result);
        if (queue.length) setTimeout(open, 0);
    }

    okBtn.addEventListener('click', function () {
        close(current && current.mode === 'confirm' ? true : undefined);
    });
    cancelBtn.addEventListener('click', function () { close(false); });
    box.addEventListener('click', function (e) {
        if (e.target === box) close(current && current.mode === 'confirm' ? false : undefined);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && busy) close(current && current.mode === 'confirm' ? false : undefined);
    });

    window.ssAlert = function (message, title) {
        return new Promise(function (resolve) {
            queue.push({ mode: 'alert', message: message, title: title || 'Perhatian', okLabel: 'Oke', resolve: resolve });
            open();
        });
    };
    window.ssConfirm = function (message, okLabel, title) {
        return new Promise(function (resolve) {
            queue.push({ mode: 'confirm', message: message, title: title || 'Konfirmasi', okLabel: okLabel || 'Ya', resolve: resolve });
            open();
        });
    };
    // Untuk onsubmit="return ssConfirmSubmit(this, '...')". submit() bawaan
    // tidak memicu handler submit lagi, jadi tidak ada loop.
    window.ssConfirmSubmit = function (form, message, okLabel, title) {
        window.ssConfirm(message, okLabel, title).then(function (ok) {
            if (ok) form.submit();
        });
        return false;
    };
})();
</script>
