{{-- Modal Detail Siswa — seluruh isi diisi JS dari data-student tombol Detail --}}
<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="ss-card max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div id="modal-avatar" class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">-</div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" id="modal-nama">-</h3>
                    <p class="text-xs text-slate-400" id="modal-nis">-</p>
                </div>
            </div>
            <button onclick="closeDetailModal()" title="Tutup detail"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400">Jenis Kelamin</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5" id="modal-gender">-</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400">Status</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5" id="modal-status">-</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400">Wali Murid</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5" id="modal-wali">-</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400">Nomor WhatsApp Wali</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5" id="modal-wa">-</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 sm:col-span-2">
                <span class="text-slate-400">Alamat Tempat Tinggal</span>
                <p class="font-semibold text-slate-800 dark:text-white mt-0.5" id="modal-alamat">-</p>
            </div>
        </div>

        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Riwayat Tagihan Terbaru</h4>
            <div id="modal-bills" class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <p class="py-6 text-center text-xs text-slate-400">Memuat riwayat…</p>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" onclick="closeDetailModal()"
                class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Tutup</button>
            <a href="#" id="modal-edit-link"
                class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Edit Siswa</a>
        </div>
    </div>
</div>

<script>
function openDetailModal(id, btn) {
    if (!btn || !btn.getAttribute) {
        btn = document.querySelector('button[onclick^="openDetailModal(' + id + '"]');
    }
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '') : ''; };
    var d = {
        id: g('id') || id,
        name: g('name'),
        nis: g('nis'),
        nisn: g('nisn'),
        gender: g('gender'),
        status: g('status'),
        kelas: g('kelas'),
        wali: g('wali'),
        wa: g('wa'),
        alamat: g('alamat'),
    };

    var val = function (v) { return (v === null || v === undefined || v === '') ? '-' : v; };
    document.getElementById('modal-avatar').textContent = (d.name || '?').trim().charAt(0).toUpperCase();
    document.getElementById('modal-nama').textContent = val(d.name);
    document.getElementById('modal-nis').textContent = 'NIS: ' + val(d.nis) + (d.nisn ? ' • NISN: ' + d.nisn : '') + ' • ' + val(d.kelas);
    document.getElementById('modal-gender').textContent = d.gender === 'P' ? 'Perempuan' : (d.gender === 'L' ? 'Laki-laki' : '-');
    document.getElementById('modal-status').textContent = d.status ? d.status.charAt(0).toUpperCase() + d.status.slice(1) : '-';
    document.getElementById('modal-wali').textContent = val(d.wali);
    document.getElementById('modal-wa').textContent = val(d.wa);
    document.getElementById('modal-alamat').textContent = val(d.alamat);

    // Klik Edit di modal detail → tutup detail, buka popup edit dengan data baris tsb.
    var editLink = document.getElementById('modal-edit-link');
    if (editLink) {
        editLink.onclick = function (e) {
            e.preventDefault();
            closeDetailModal();
            var eb = document.querySelector('button[onclick^="openEditModal(' + d.id + '"]');
            openEditModal(d.id, eb || btn);
        };
    }

    // Riwayat tagihan nyata via endpoint JSON.
    var box = document.getElementById('modal-bills');
    box.innerHTML = '<p class="py-6 text-center text-xs text-slate-400">Memuat riwayat…</p>';
    fetch('{{ url('/admin/siswa') }}/' + d.id + '/bills', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (rows) {
            if (!rows || !rows.length) {
                box.innerHTML = '<p class="py-6 text-center text-xs text-slate-400">Siswa ini belum memiliki tagihan.</p>';
                return;
            }
            var pill = function (s) {
                var cls = s === 'paid' ? 'pill-success' : (s === 'overdue' ? 'pill-danger' : 'pill-warning');
                var lbl = s === 'paid' ? 'Lunas' : (s === 'overdue' ? 'Menunggak' : (s === 'partial' ? 'Sebagian' : 'Belum bayar'));
                return '<span class="pill ' + cls + ' text-[10px] !py-0.5">' + lbl + '</span>';
            };
            var html = '<table class="w-full text-left text-xs"><thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 text-[10px] uppercase font-semibold">'
                + '<tr><th class="py-2 px-3">Invoice</th><th class="py-2 px-3">Kategori</th>'
                + '<th class="py-2 px-3">Nominal</th><th class="py-2 px-3 text-center">Status</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">';
            rows.forEach(function (b) {
                html += '<tr><td class="py-2.5 px-3 font-semibold text-blue-600 dark:text-blue-400">' + b.bill_code + '</td>'
                    + '<td class="py-2.5 px-3">' + b.category + '</td>'
                    + '<td class="py-2.5 px-3">Rp ' + b.amount + '</td>'
                    + '<td class="py-2.5 px-3 text-center">' + pill(b.status) + '</td></tr>';
            });
            box.innerHTML = html + '</tbody></table>';
        })
        .catch(function () {
            box.innerHTML = '<p class="py-6 text-center text-xs text-rose-500">Gagal memuat riwayat tagihan.</p>';
        });

    document.getElementById('detail-modal').classList.remove('hidden');
}
function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}
</script>
