<div class="space-y-4">
    {{-- Profil Sekolah Card --}}
    <div class="ss-card space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Profil Sekolah</h2>
            <p class="text-xs text-slate-400">Tampil di invoice & portal siswa</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NAMA SEKOLAH</label>
                <input type="text" name="school_name" value="{{ $settings['school_name'] ?? '' }}" placeholder="Nama sekolah" class="ss-input text-xs !py-2">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NPSN / NSM</label>
                <input type="text" name="npsn" value="{{ $settings['npsn'] ?? '' }}" placeholder="NPSN" class="ss-input text-xs !py-2">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">EMAIL RESMI</label>
                <input type="email" name="school_email" value="{{ $settings['school_email'] ?? '' }}" placeholder="email@sekolah.sch.id" class="ss-input text-xs !py-2">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">TELEPON</label>
                <input type="tel" name="school_phone" value="{{ $settings['school_phone'] ?? '' }}" placeholder="(021) ..." class="ss-input text-xs !py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">ALAMAT LENGKAP</label>
                <input type="text" name="school_address" value="{{ $settings['school_address'] ?? '' }}" placeholder="Alamat sekolah" class="ss-input text-xs !py-2">
            </div>
        </div>
    </div>

    {{-- Tahun Ajaran & Semester Card --}}
    <div class="ss-card space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Tahun Ajaran & Semester</h2>
            <p class="text-xs text-slate-400">Dipakai subtitle Data Siswa & Laporan</p>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">TAHUN AJARAN</label>
                <input type="text" name="academic_year" value="{{ $settings['academic_year'] ?? '' }}" placeholder="2026/2027" class="ss-input text-xs !py-2">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">SEMESTER AKTIF</label>
                <select name="active_semester" class="ss-input text-xs !py-2">
                    <option value="Ganjil" {{ ($settings['active_semester'] ?? 'Ganjil') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ ($settings['active_semester'] ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
        </div>
    </div>
</div>
