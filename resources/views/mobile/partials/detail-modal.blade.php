{{-- Popup Detail Siswa mobile — tetap di tampilan mobile, riwayat via JSON /admin/siswa/{id}/bills --}}
<div id="m-detail" class="m-modal hidden" role="dialog" aria-modal="true" aria-label="Detail siswa">
    <div class="m-modal-card">
        <div class="m-modal-head">
            <div class="m-stu">
                <div class="m-stu-av" id="m-d-avatar">-</div>
                <div class="m-stu-mid">
                    <div class="m-stu-name" id="m-d-nama">-</div>
                    <div class="m-stu-sub" id="m-d-nis">-</div>
                </div>
            </div>
            <button type="button" class="m-modal-x" onclick="mCloseDetail()" aria-label="Tutup detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="m-modal-grid">
            <div class="m-modal-box"><span>Jenis Kelamin</span><p id="m-d-gender">-</p></div>
            <div class="m-modal-box"><span>Status</span><p id="m-d-status">-</p></div>
            <div class="m-modal-box"><span>Wali Murid</span><p id="m-d-wali">-</p></div>
            <div class="m-modal-box"><span>WA Wali</span><p id="m-d-wa">-</p></div>
            <div class="m-modal-box m-span2"><span>Alamat</span><p id="m-d-alamat">-</p></div>
        </div>
        <div class="m-sec-title" style="margin-top:4px">Riwayat Tagihan</div>
        <div id="m-d-bills" class="m-modal-bills"><div class="m-fdesc">Memuat riwayat…</div></div>
        <div class="m-actions">
            <button type="button" class="m-btn" onclick="mCloseDetail()">Tutup</button>
            <button type="button" class="m-btn m-btn-primary" id="m-d-edit">Edit</button>
        </div>
    </div>
</div>

<script>
function mOpenDetail(id, btn) {
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '') : ''; };
    var d = { id: g('id') || id, name: g('name'), nis: g('nis'), nisn: g('nisn'), gender: g('gender'), status: g('status'), kelas: g('kelas'), wali: g('wali'), wa: g('wa'), alamat: g('alamat') };
    var val = function (v) { return (v === null || v === undefined || v === '') ? '-' : v; };
    document.getElementById('m-d-avatar').textContent = (d.name || '?').trim().charAt(0).toUpperCase();
    document.getElementById('m-d-nama').textContent = val(d.name);
    document.getElementById('m-d-nis').textContent = 'NIS ' + val(d.nis) + (d.nisn && d.nisn !== '-' ? ' · ' + d.nisn : '') + ' · ' + val(d.kelas);
    document.getElementById('m-d-gender').textContent = d.gender === 'P' ? 'Perempuan' : (d.gender === 'L' ? 'Laki-laki' : '-');
    document.getElementById('m-d-status').textContent = d.status ? d.status.charAt(0).toUpperCase() + d.status.slice(1) : '-';
    document.getElementById('m-d-wali').textContent = val(d.wali);
    document.getElementById('m-d-wa').textContent = val(d.wa);
    document.getElementById('m-d-alamat').textContent = val(d.alamat);
    document.getElementById('m-d-edit').onclick = function () { mCloseDetail(); mOpenEdit(d.id, btn); };
    var box = document.getElementById('m-d-bills');
    box.innerHTML = '<div class="m-fdesc">Memuat riwayat…</div>';
    fetch('/admin/siswa/' + d.id + '/bills', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (rows) {
            if (!rows || !rows.length) { box.innerHTML = '<div class="m-fdesc">Siswa ini belum memiliki tagihan.</div>'; return; }
            var html = '';
            rows.forEach(function (b) {
                var pill = b.status === 'paid' ? 'm-pill-ok' : (b.status === 'overdue' ? 'm-pill-err' : 'm-pill-warn');
                var lbl = b.status === 'paid' ? 'Lunas' : (b.status === 'overdue' ? 'Menunggak' : (b.status === 'partial' ? 'Sebagian' : 'Belum bayar'));
                html += '<div class="m-bill"><div><div class="m-stu-name">' + b.bill_code + '</div><div class="m-stu-sub">' + b.category + ' · Rp ' + b.amount + '</div></div><span class="m-pill ' + pill + '">' + lbl + '</span></div>';
            });
            box.innerHTML = html;
        })
        .catch(function () { box.innerHTML = '<div class="m-fdesc">Gagal memuat riwayat tagihan.</div>'; });
    document.getElementById('m-detail').classList.remove('hidden');
}
function mCloseDetail() { document.getElementById('m-detail').classList.add('hidden'); }
document.getElementById('m-detail').addEventListener('click', function (e) { if (e.target === this) mCloseDetail(); });
</script>
