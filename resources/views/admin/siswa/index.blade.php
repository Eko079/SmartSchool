@extends('layouts.admin')
@section('title', 'Data Siswa')
@section('breadcrumb', 'Master Data / Data Siswa')
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
                    <button type="button" title="Impor CSV"
                        onclick="document.getElementById('import-modal').classList.remove('hidden')"
                        class="flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs">
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
                    <select name="class_id" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                        <option value="">Semua Kelas</option>
                        @foreach($classes ?? [] as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    </select>
                    <select name="per_page" onchange="this.form.submit()" title="Jumlah baris per halaman"
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }} / halaman</option>
                        @endforeach
                    </select>
                    @if(request()->anyFilled(['q', 'class_id', 'status']))
                        <a href="{{ route('admin.siswa', request()->only(['sort', 'dir'])) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>
        </x-slot>

        <x-slot name="head">
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
            <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">{{ $st->nis }}</td>
            <td class="py-3 px-3">
                <div class="font-medium text-slate-800 dark:text-white">{{ $st->name }}</div>
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
                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:bg-slate-50">Detail</button>
                    <button type="button" title="Ubah data {{ $st->name }}"
                        onclick="openEditModal({{ $st->id }}, this)"
                        data-id="{{ $st->id }}"
                        data-name="{{ $st->name }}"
                        data-nis="{{ $st->nis }}"
                        data-nisn="{{ $st->nisn ?? '' }}"
                        data-gender="{{ $st->gender }}"
                        data-status="{{ $st->status }}"
                        data-class-id="{{ $st->class_id }}"
                        data-wali="{{ $st->guardian_name ?? '' }}"
                        data-wa="{{ $st->guardian_phone ?? '' }}"
                        data-alamat="{{ $st->address ?? '' }}"
                        class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Edit</button>
                    <form action="{{ route('admin.siswa.destroy', $st->id) }}" method="POST"
                        onsubmit="return confirm('Hapus {{ addslashes($st->name) }} (NIS {{ $st->nis }})? Bila masih punya tagihan, penghapusan ditolak.');"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus {{ $st->name }}"
                            class="rounded-md border border-rose-200 dark:border-rose-900 px-2 py-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="py-6 text-center text-slate-400">
                @if(request()->anyFilled(['q', 'class_id', 'status']))
                    Tidak cocok dengan filter. <a href="{{ route('admin.siswa') }}" class="text-blue-600 hover:underline">Tampilkan semua</a>.
                @else
                    Belum ada data siswa. Klik <strong>Tambah Siswa</strong> atau <strong>Import CSV</strong>.
                @endif
            </td>
        </tr>
        @endforelse

        @if(isset($students) && $students->hasPages())
        <x-slot name="pagination">
            {{ $students->links() }}
        </x-slot>
        @endif
    </x-ss-table>

    @include('admin.siswa.detail-modal')
    @include('admin.siswa.create-modal')
    @include('admin.siswa.edit-modal')
    @include('admin.siswa.import-modal')

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
                tr.innerHTML = '<td colspan="6" class="py-6 text-center text-slate-400">Tidak ada hasil untuk “'
                    + input.value.replace(/</g, '&lt;') + '”.</td>';
                tbody.appendChild(tr);
            }
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(applyFilter, 120);
        });

        // Mencegah submit saat Enter — hasil sudah tampil instan.
        input.closest('form').addEventListener('submit', function (e) {
            e.preventDefault();
            applyFilter();
        });
    })();
    </script>
@endsection
