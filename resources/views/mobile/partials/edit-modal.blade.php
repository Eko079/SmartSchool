{{-- Popup Edit Siswa mobile — submit PUT ke /admin/siswa/{id}, tetap di tampilan mobile --}}
<div id="m-edit" class="m-modal hidden" role="dialog" aria-modal="true" aria-label="Ubah siswa">
    <div class="m-modal-card">
        <div class="m-modal-head">
            <div class="m-stu">
                <div class="m-stu-av" id="m-e-avatar">-</div>
                <div class="m-stu-mid">
                    <div class="m-stu-name">Ubah Data Siswa</div>
                    <div class="m-stu-sub" id="m-e-sub">-</div>
                </div>
            </div>
            <button type="button" class="m-modal-x" onclick="mCloseEdit()" aria-label="Tutup edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="m-e-form" method="POST" action="">
            @csrf
            @method('PUT')
            <label class="m-label">NIS</label>
            <input type="text" name="nis" id="m-e-nis" class="m-input" required>
            <label class="m-label" style="margin-top:8px">NISN</label>
            <input type="text" name="nisn" id="m-e-nisn" class="m-input">
            <label class="m-label" style="margin-top:8px">Nama Lengkap</label>
            <input type="text" name="name" id="m-e-name" class="m-input" required>
            <div class="m-row2" style="margin-top:8px">
                <div><label class="m-label">Kelas</label><select name="class_id" id="m-e-class" class="m-select" required>@foreach($classes ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div><label class="m-label">Gender</label><select name="gender" id="m-e-gender" class="m-select"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                <div><label class="m-label">Status</label><select name="status" id="m-e-status" class="m-select"><option value="aktif">Aktif</option><option value="cuti">Cuti</option><option value="lulus">Lulus</option></select></div>
            </div>
            <div class="m-row2" style="margin-top:8px">
                <div><label class="m-label">Nama Wali</label><input type="text" name="guardian_name" id="m-e-wali" class="m-input"></div>
                <div><label class="m-label">WA Wali</label><input type="tel" name="guardian_phone" id="m-e-wa" class="m-input"></div>
            </div>
            <label class="m-label" style="margin-top:8px">Alamat</label>
            <textarea name="address" id="m-e-alamat" rows="2" class="m-input"></textarea>
            <div class="m-actions" style="margin-top:12px">
                <button type="button" class="m-btn" onclick="mCloseEdit()">Batal</button>
                <button type="submit" class="m-btn m-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function mOpenEdit(id, btn) {
    var g = function (k) { return btn ? (btn.getAttribute('data-' + k) || '') : ''; };
    var name = g('name'), nis = g('nis');
    document.getElementById('m-e-avatar').textContent = (name || '?').trim().charAt(0).toUpperCase();
    document.getElementById('m-e-sub').textContent = 'NIS ' + (nis || '-');
    document.getElementById('m-e-nis').value = nis;
    document.getElementById('m-e-nisn').value = g('nisn');
    document.getElementById('m-e-name').value = name;
    document.getElementById('m-e-class').value = g('class-id');
    document.getElementById('m-e-gender').value = g('gender') || 'L';
    document.getElementById('m-e-status').value = g('status') || 'aktif';
    document.getElementById('m-e-wali').value = g('wali');
    document.getElementById('m-e-wa').value = g('wa');
    document.getElementById('m-e-alamat').value = g('alamat');
    document.getElementById('m-e-form').action = '/admin/siswa/' + (g('id') || id);
    document.getElementById('m-edit').classList.remove('hidden');
}
function mCloseEdit() { document.getElementById('m-edit').classList.add('hidden'); }
document.getElementById('m-edit').addEventListener('click', function (e) { if (e.target === this) mCloseEdit(); });
</script>
