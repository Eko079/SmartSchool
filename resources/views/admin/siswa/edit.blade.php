@extends('layouts.admin')
@section('title', 'Ubah Siswa')
@section('breadcrumb', 'Master Data / Data Siswa / Ubah')
@section('page-title', 'Ubah Data Siswa')
@section('page-subtitle', 'Perbarui identitas, kelas, dan kontak wali siswa.')

@section('content')
<form method="POST" action="{{ route('admin.siswa.update', $student->id) }}" class="ss-card max-w-2xl space-y-4 text-xs">
    @csrf
    @method('PUT')

    <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $student->name }}</div>
            <div class="text-[11px] text-slate-400">NIS: {{ $student->nis }}{{ $student->nisn ? ' • NISN: ' . $student->nisn : '' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS <span class="text-rose-500">*</span></label>
            <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="ss-input text-xs !py-2" required>
        </div>
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" class="ss-input text-xs !py-2">
        </div>
    </div>

    <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $student->name) }}" class="ss-input text-xs !py-2" required>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas <span class="text-rose-500">*</span></label>
            <select name="class_id" class="ss-input text-xs !py-2" required>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ old('class_id', $student->class_id) == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
            <select name="gender" class="ss-input text-xs !py-2">
                <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
            <select name="status" class="ss-input text-xs !py-2">
                @foreach(['aktif', 'nonaktif', 'lulus'] as $s)
                    <option value="{{ $s }}" {{ old('status', $student->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wali</label>
            <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" class="ss-input text-xs !py-2">
        </div>
        <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Wali</label>
            <input type="tel" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}" class="ss-input text-xs !py-2">
        </div>
    </div>

    <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
        <textarea name="address" rows="2" class="ss-input text-xs !py-2">{{ old('address', $student->address) }}</textarea>
    </div>

    <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('admin.siswa') }}" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
    </div>
</form>
@endsection
