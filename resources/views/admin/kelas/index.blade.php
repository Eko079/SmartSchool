@extends('layouts.admin')
@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')
@section('page-subtitle', 'Kelas menentukan pemetaan kenaikan. Format nama dikunci: Tingkat Jurusan Nomor.')
@section('content')
    <div class="ss-card space-y-4 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Kelas</h2>
                <p class="text-xs text-slate-400">{{ $classes->count() }} kelas • tahun ajaran diatur di Pengaturan</p>
            </div>
            <button type="button" title="Tambah kelas"
                onclick="document.getElementById('kelas-create-modal').classList.remove('hidden')"
                class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kelas
            </button>
        </div>

        <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="py-2.5 px-3">Nama Kelas</th>
                        <th class="py-2.5 px-3">Tingkat</th>
                        <th class="py-2.5 px-3">Jurusan</th>
                        <th class="py-2.5 px-3 text-center">Isi / Kapasitas</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $cls)
                    <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <td class="py-3 px-3 font-semibold text-slate-800 dark:text-white">{{ $cls->name }}</td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300">{{ $cls->grade }}</td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300">{{ $cls->major ?? '-' }}</td>
                        <td class="py-3 px-3 text-center text-slate-600 dark:text-slate-300">{{ $cls->students_count }}{{ $cls->capacity ? ' / ' . $cls->capacity : '' }}</td>
                        <td class="py-3 px-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" title="Ubah {{ $cls->name }}"
                                    onclick="openKelasEdit({{ $cls->id }}, this)"
                                    data-name="{{ $cls->name }}" data-major="{{ $cls->major ?? '' }}" data-capacity="{{ $cls->capacity ?? '' }}"
                                    class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Edit</button>
                                <form action="{{ route('admin.kelas.destroy', $cls->id) }}" method="POST" class="inline"
                                    onsubmit="return ssConfirmSubmit(this, 'Hapus kelas {{ addslashes($cls->name) }}?', 'Hapus')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus {{ $cls->name }}"
                                        class="rounded-md border border-rose-200 dark:border-rose-900 px-2 py-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="kelas-create-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Kelas</h3>
                <button onclick="document.getElementById('kelas-create-modal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" class="ss-input text-xs !py-2" placeholder="Contoh: X IPA 1" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="major" class="ss-input text-xs !py-2" placeholder="IPA" required>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas</label>
                        <input type="number" name="capacity" min="1" max="100" class="ss-input text-xs !py-2" placeholder="Opsional">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="document.getElementById('kelas-create-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Ubah --}}
    <div id="kelas-edit-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="ss-card max-w-md w-full space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Ubah Kelas</h3>
                <button onclick="document.getElementById('kelas-edit-modal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="kelas-edit-form" method="POST" action="" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="kelas-edit-name" class="ss-input text-xs !py-2" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="major" id="kelas-edit-major" class="ss-input text-xs !py-2" required>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas</label>
                        <input type="number" name="capacity" id="kelas-edit-capacity" min="1" max="100" class="ss-input text-xs !py-2">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="document.getElementById('kelas-edit-modal').classList.add('hidden')" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openKelasEdit(id, btn) {
        document.getElementById('kelas-edit-form').action = '/admin/kelas/' + id;
        document.getElementById('kelas-edit-name').value = btn.getAttribute('data-name') || '';
        document.getElementById('kelas-edit-major').value = btn.getAttribute('data-major') || '';
        document.getElementById('kelas-edit-capacity').value = btn.getAttribute('data-capacity') || '';
        document.getElementById('kelas-edit-modal').classList.remove('hidden');
    }
    </script>
@endsection
