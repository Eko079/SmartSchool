@extends('layouts.admin')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle')
    Kelola data siswa tahun ajaran {{ $academicYear ?? '' }}. Cari, filter, dan urutkan berdasarkan kolom tabel.
@endsection

@section('content')
    @include('admin.siswa.stats')

    <x-ss-table>
        <x-slot name="filters">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Siswa</h2>
                    <p class="text-xs text-slate-400">Kelola NIS, kelas dan status</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.siswa.template') }}" title="Download template Excel"
                        class="flex items-center gap-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950 px-3 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Template
                    </a>
                    <button type="button" title="Impor CSV"
                        onclick="document.getElementById('import-modal').classList.remove('hidden')"
                        class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition shadow-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Import CSV
                    </button>
                    <button type="button" title="Tambah siswa"
                        onclick="document.getElementById('create-modal').classList.remove('hidden')"
                        class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Siswa
                    </button>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.siswa') }}" class="flex flex-wrap items-center justify-between gap-3 pt-3">
                <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[260px]">
                    <div class="relative flex-1 min-w-[180px] max-w-sm">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" name="q" id="siswa-search" value="{{ request('q') }}" placeholder="Cari NIS, NISN, nama..."
                            autocomplete="off"
                            class="ss-input text-xs !py-2 !pl-9">
                        {{-- Pertahankan sorting saat mencari (diisi ulang otomatis oleh JS live-search) --}}
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <input type="hidden" name="dir" value="{{ request('dir') }}">
                    </div>
                    @php
                        $pickedClasses = array_map('intval', (array) request('class_id', []));
                        $totalClasses = ($classes ?? collect())->count();
                        $kelasLabel = (! $pickedClasses || count($pickedClasses) === $totalClasses) ? 'Semua' : count($pickedClasses) . ' kelas';
                    @endphp
                    <div class="relative">
                        <button type="button" id="kelas-drop-btn"
                            class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                            Kelas: <span id="kelas-drop-label" class="font-semibold">{{ $kelasLabel }}</span> ▾
                        </button>
                        <div id="kelas-drop" class="hidden absolute z-30 mt-1 w-[38rem] max-w-[92vw] max-h-80 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 shadow-lg text-xs">
                            <div class="flex flex-wrap gap-2 pb-2 mb-2 border-b border-slate-200 dark:border-slate-700 text-[11px] font-semibold">
                                <button type="button" onclick="kelasDropReset()" class="text-blue-600" title="Langsung tampil semua (tanpa filter kelas)">Semua</button>
                                <button type="button" onclick="kelasDropAll(false)" class="text-slate-400">Kosongkan</button>
                                @foreach(['X', 'XI', 'XII'] as $g)
                                    <button type="button" onclick="kelasDropGrade('{{ $g }}')" class="text-blue-600" title="Langsung tampil semua kelas {{ $g }}">Hanya {{ $g }}</button>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach(['X', 'XI', 'XII'] as $g)
                                    <div class="min-w-[11rem]">
                                        <div class="font-bold text-slate-500 mb-1">Kelas {{ $g }}</div>
                                        @foreach(($classes ?? collect())->where('grade', $g) as $cls)
                                            <label class="flex items-center gap-2.5 py-1.5 cursor-pointer whitespace-nowrap text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="class_id[]" value="{{ $cls->id }}" data-grade="{{ $cls->grade }}"
                                                    {{ in_array($cls->id, $pickedClasses, true) ? 'checked' : '' }}
                                                    class="kelas-check rounded text-blue-600 w-4 h-4" onchange="kelasDropTick()">
                                                <span class="text-[13px] font-medium">{{ $cls->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="mt-2 w-full rounded-lg bg-blue-600 py-2 font-bold text-white hover:bg-blue-700">Tampilkan</button>
                        </div>
                    </div>
                    <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    </select>
                    <select name="per_page" onchange="this.form.submit()" title="{{ ($isFiltered ?? false) ? 'Diabaikan saat filter aktif — menampilkan semua yang cocok' : 'Jumlah baris per halaman' }}"
                        {{ ($isFiltered ?? false) ? 'disabled' : '' }}
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none {{ ($isFiltered ?? false) ? 'opacity-40 cursor-not-allowed' : '' }}">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }} / halaman</option>
                        @endforeach
                    </select>
                    @if(request()->anyFilled(['q', 'class_id', 'status']))
                        <a href="{{ route('admin.siswa', request()->only(['sort', 'dir'])) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
                        <span class="text-[11px] text-slate-400">Menampilkan semua {{ ($isFiltered ?? false) ? $students->count() : $students->total() }} yang cocok (tanpa halaman).</span>
                    @endif
                </div>
            </form>
            <div class="flex flex-wrap items-center gap-2 pt-3">
                <span class="text-[11px] text-slate-400">Aksi massal (<span id="siswa-checked-count">0</span> dipilih):</span>
                <button type="button" onclick="siswaBulkPromote()" class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950 px-3 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900">Naik Kelas</button>
                <button type="button" onclick="siswaBulkStatus('lulus')" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Tandai Lulus</button>
                <button type="button" onclick="siswaBulkStatus('nonaktif')" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Tandai Nonaktif</button>
            </div>
        </x-slot>

        <x-slot name="head">
            <th class="py-2.5 px-3 w-8"><input type="checkbox" id="siswa-check-all" title="Pilih yang tampil" class="rounded text-blue-600"></th>
            <x-ss-th sort="nis" label="NIS" />
            <x-ss-th sort="name" label="Nama Siswa" />
            <th class="py-2.5 px-3">Wali / No. WA</th>
            <x-ss-th sort="class" label="Kelas" />
            <x-ss-th sort="status" label="Status" align="center" />
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>

        @forelse($students ?? [] as $st)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition"
            title="{{ $st->name }} • NIS {{ $st->nis }} • {{ $st->classRoom->name ?? 'tanpa kelas' }}">
            @php $tgt = \App\Models\Student::promotionTarget($st, $classes ?? collect()); @endphp
            <td class="py-3 px-3"><input type="checkbox" class="siswa-check rounded text-blue-600" value="{{ $st->id }}"
                data-name="{{ $st->name }}" data-from="{{ $st->classRoom->name ?? '-' }}"
                data-to="{{ $tgt['action'] === 'naik' && $tgt['class'] ? $tgt['class']->name : '' }}"></td>
            <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">{{ $st->nis }}</td>
            <td class="py-3 px-3">
                <div class="font-medium text-slate-800 dark:text-white">{{ $st->name }}@if(in_array($st->id, $noBillIds ?? []))<span class="ml-1.5 inline-block h-2 w-2 rounded-full bg-amber-400 align-middle" title="Belum punya tagihan semester ini — buka Detail untuk membuatkan"></span>@endif</div>
                <div class="text-[11px] text-slate-400">NISN: {{ $st->nisn ?? '-' }} • {{ $st->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            </td>
            <td class="py-3 px-3">
                <div class="text-slate-800 dark:text-slate-200">{{ $st->guardian_name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $st->guardian_phone ?? '-' }}</div>
            </td>
            <td class="py-3 px-3 font-medium text-slate-700 dark:text-slate-300">{{ $st->classRoom->name ?? '-' }}</td>
            <td class="py-3 px-3 text-center">
                <x-ss-pill :status="$st->status" />
            </td>
            <td class="py-3 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                    <button type="button" title="Lihat detail {{ $st->name }}"
                        onclick="openDetailModal({{ $st->id }}, this)"
                        data-id="{{ $st->id }}"
                        data-name="{{ $st->name }}"
                        data-nis="{{ $st->nis }}"
                        data-nisn="{{ $st->nisn ?? '' }}"
                        data-gender="{{ $st->gender }}"
                        data-status="{{ $st->status }}"
                        data-kelas="{{ $st->classRoom->name ?? '-' }}"
                        data-wali="{{ $st->guardian_name ?? '' }}"
                        data-wa="{{ $st->guardian_phone ?? '' }}"
                        data-alamat="{{ $st->address ?? '' }}"
                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 transition">Detail</button>
                    <button type="button" title="Ubah data {{ $st->name }}"
                        onclick="openEditModal({{ $st->id }}, this)"
                        data-id="{{ $st->id }}"
                        data-name="{{ $st->name }}"
                        data-nis="{{ $st->nis }}"
                        data-nisn="{{ $st->nisn ?? '' }}"
                        data-gender="{{ $st->gender }}"
                        data-status="{{ $st->status }}"
                        data-class-id="{{ $st->class_id }}"
                        data-entry="{{ $st->entry_year ?? '' }}"
                        data-wali="{{ $st->guardian_name ?? '' }}"
                        data-wa="{{ $st->guardian_phone ?? '' }}"
                        data-alamat="{{ $st->address ?? '' }}"
                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Edit</button>
                    <form action="{{ route('admin.siswa.destroy', $st->id) }}" method="POST"
                        onsubmit="return ssConfirmSubmit(this, 'Hapus {{ addslashes($st->name) }} (NIS {{ $st->nis }})? Bila masih punya tagihan, penghapusan ditolak.', 'Hapus')"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus {{ $st->name }}"
                            class="rounded-md border border-rose-200 dark:border-rose-900 px-2 py-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950 transition">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-6 text-center text-slate-400">
                @if(request()->anyFilled(['q', 'class_id', 'status']))
                    Tidak cocok dengan filter. <a href="{{ route('admin.siswa') }}" class="text-blue-600 hover:underline">Tampilkan semua</a>.
                @else
                    Belum ada data siswa. Klik <strong>Tambah Siswa</strong> atau <strong>Import CSV</strong>.
                @endif
            </td>
        </tr>
        @endforelse

        @if(isset($students) && method_exists($students, 'hasPages') && $students->hasPages())
        <x-slot name="pagination">
            {{ $students->links() }}
        </x-slot>
        @endif
    </x-ss-table>

    @include('admin.siswa.detail-modal')
    @include('admin.siswa.create-modal')
    @include('admin.siswa.edit-modal')
    @include('admin.siswa.import-modal')

    <script>
    // Habis tambah siswa tanpa tagihan → buka popup detailnya otomatis.
    (function () {
        var id = new URLSearchParams(window.location.search).get('open_detail');
        if (!id) return;
        var btn = document.querySelector('button[onclick^="openDetailModal(' + id + '"]');
        if (btn && typeof openDetailModal === 'function') openDetailModal(id, btn);
        history.replaceState(null, '', window.location.pathname);
    })();
    </script>
    <script>
    // Aksi massal: centang + tombol.
    function siswaChecked() {
        var list = [];
        document.querySelectorAll('.siswa-check').forEach(function (c) {
            if (c.checked) list.push(c);
        });
        return list;
    }
    function siswaCountTick() {
        document.getElementById('siswa-checked-count').textContent = siswaChecked().length;
    }
    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList && e.target.classList.contains('siswa-check')) siswaCountTick();
    });
    document.getElementById('siswa-check-all').addEventListener('change', function () {
        var on = this.checked;
        document.querySelectorAll('.siswa-check').forEach(function (c) {
            if (c.offsetParent !== null) c.checked = on;
        });
        siswaCountTick();
    });
    // Dropdown filter multi-kelas.
    function kelasDropTick() {
        var boxes = document.querySelectorAll('.kelas-check');
        var n = 0;
        boxes.forEach(function (c) { if (c.checked) n++; });
        document.getElementById('kelas-drop-label').textContent = (n === 0 || n === boxes.length) ? 'Semua' : n + ' kelas';
    }
    function kelasDropAll(on) {
        document.querySelectorAll('.kelas-check').forEach(function (c) { c.checked = on; });
        kelasDropTick();
    }
    // Submit biasa (bukan requestSubmit) agar tidak ada listener yang bisa menahan navigasi.
    function kelasDropSubmit() {
        var d = document.getElementById('kelas-drop');
        var f = d ? d.closest('form') : null;
        if (f) f.submit();
    }
    // Pintasan: langsung terapkan tanpa perlu klik Tampilkan / ubah halaman dulu.
    function kelasDropReset() {
        document.querySelectorAll('.kelas-check').forEach(function (c) { c.checked = false; });
        kelasDropTick();
        kelasDropSubmit();
    }
    function kelasDropGrade(g) {
        document.querySelectorAll('.kelas-check').forEach(function (c) { c.checked = (c.getAttribute('data-grade') === g); });
        kelasDropTick();
        kelasDropSubmit();
    }
    document.getElementById('kelas-drop-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        document.getElementById('kelas-drop').classList.toggle('hidden');
    });
    document.addEventListener('click', function (e) {
        var d = document.getElementById('kelas-drop');
        if (d && !d.classList.contains('hidden') && !d.contains(e.target)) d.classList.add('hidden');
    });
    kelasDropTick();
    function siswaPost(url, ids, extra) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = url;
        var t = document.createElement('input');
        t.type = 'hidden'; t.name = '_token';
        t.value = (document.querySelector('input[name="_token"]') || {}).value || '';
        f.appendChild(t);
        ids.forEach(function (id) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = 'ids[]'; h.value = id;
            f.appendChild(h);
        });
        Object.keys(extra || {}).forEach(function (k) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = k; h.value = extra[k];
            f.appendChild(h);
        });
        document.body.appendChild(f);
        f.submit();
    }
    function siswaBulkStatus(action) {
        var list = siswaChecked();
        if (!list.length) { ssAlert('Centang dulu siswanya.'); return; }
        var label = action === 'lulus' ? 'lulus' : 'nonaktif';
        ssConfirm('Tandai ' + list.length + ' siswa sebagai ' + label + '?', 'Tandai').then(function (ok) {
            if (!ok) return;
            siswaPost('/admin/siswa/bulk-status', list.map(function (c) { return c.value; }), { action: action });
        });
    }
    function siswaBulkPromote() {
        var list = siswaChecked();
        if (!list.length) { ssAlert('Centang dulu siswanya.'); return; }
        var lines = [];
        var blocked = 0;
        list.forEach(function (c) {
            var to = c.getAttribute('data-to') || '';
            if (to) lines.push(c.getAttribute('data-name') + ': ' + c.getAttribute('data-from') + ' → ' + to);
            else { lines.push(c.getAttribute('data-name') + ': tidak bisa naik (dilewati)'); blocked++; }
        });
        var preview = lines.slice(0, 12).join('\n') + (lines.length > 12 ? '\n... +' + (lines.length - 12) + ' lainnya' : '');
        ssConfirm('Naikkan ' + (list.length - blocked) + ' siswa? (' + blocked + ' dilewati)\n\n' + preview, 'Naikkan').then(function (ok) {
            if (!ok) return;
            siswaPost('/admin/siswa/bulk-promote', list.map(function (c) { return c.value; }), {});
        });
    }
    </script>

    {{-- Live search: ketik langsung filter, hapus langsung kembali semua.
      Sembunyikan pagination saat memfilter agar tidak membingungkan
      (pagination hanya relevan untuk hasil server penuh). --}}
    <script>
    (function () {
        var input = document.getElementById('siswa-search');
        if (!input) return;
        var tbody = document.getElementById('ss-table-body');
        var pager = document.getElementById('ss-pagination');
        var timer = null;

        function rowText(tr) {
            return (tr.innerText || tr.textContent || '').toLowerCase();
        }

        function applyFilter() {
            if (!tbody) return;
            var q = input.value.trim().toLowerCase();
            var filtering = q !== '';
            var rows = tbody.querySelectorAll('tr');
            var visible = 0;
            rows.forEach(function (tr) {
                // Baris pesan kosong (colspan) jangan ikut dihitung sbg data.
                if (tr.querySelector('td[colspan]')) {
                    tr.style.display = 'none';
                    return;
                }
                var hit = !filtering || rowText(tr).indexOf(q) !== -1;
                tr.style.display = hit ? '' : 'none';
                if (hit) visible++;
            });

            // Saat live-filter aktif, pagination disembunyikan (hasil hanya subset halaman ini).
            if (pager) pager.style.display = filtering ? 'none' : '';

            // Tampilkan / hapus baris "tidak ketemu" dinamis.
            var emptyId = 'siswa-live-empty';
            var old = document.getElementById(emptyId);
            if (old) old.remove();
            if (visible === 0 && filtering) {
                var tr = document.createElement('tr');
                tr.id = emptyId;
                tr.innerHTML = '<td colspan="7" class="py-6 text-center text-slate-400">Tidak ada hasil untuk “'
                    + input.value.replace(/</g, '&lt;') + '”.</td>';
                tbody.appendChild(tr);
            }
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(applyFilter, 120);
        });

        // Mencegah reload saat Enter di kolom cari (hasil sudah tampil instan,
        // dan reload akan menghilangkan centangan) — tombol Tampilkan tetap submit normal.
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    })();
    </script>
@endsection
