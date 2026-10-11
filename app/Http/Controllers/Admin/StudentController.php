<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Support\Device;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // HP => tampilan mobile otomatis (URL tetap /admin/siswa).
        if (Device::isPhone($request)) {
            return app(MobileController::class)->siswa($request);
        }

        $query = Student::with('classRoom');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        // ---------- Sorting via klik header kolom ----------
        $allowedSorts = ['nis', 'name', 'class', 'status', 'latest'];
        $sort = $request->input('sort', 'latest');
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'class') {
            // Sort berdasarkan nama kelas (tabel classes).
            $query->leftJoin('classes', 'classes.id', '=', 'students.class_id')
                ->orderBy('classes.name', $dir)
                ->orderBy('students.name', 'asc')
                ->select('students.*');
        } elseif ($sort === 'latest') {
            $query->orderBy('students.created_at', $dir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('students.' . $sort, $dir);
        }

        // ---------- Jumlah baris per halaman (10 / 25 / 50 / 100) ----------
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $students = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Student::count(),
            'aktif' => Student::where('status', 'aktif')->count(),
            'nonaktif' => Student::where('status', 'nonaktif')->count(),
            'lulus' => Student::where('status', 'lulus')->count(),
        ];

        $classes = ClassRoom::orderBy('name')->get();

        $academicYear = \App\Models\SchoolSetting::get('academic_year', date('Y') . '/' . (date('Y') + 1));

        // Penanda baris tanpa tagihan semester berjalan (1 query, bukan N+1).
        $noBillIds = [];
        $activeSemester = strtolower((string) \App\Models\SchoolSetting::get('active_semester', 'Ganjil')) === 'genap' ? 'genap' : 'ganjil';
        if ($academicYear) {
            $pageIds = $students->pluck('id')->all();
            $withBills = \App\Models\Bill::whereIn('student_id', $pageIds)
                ->where('academic_year', $academicYear)->where('semester', $activeSemester)
                ->distinct()->pluck('student_id')->all();
            $noBillIds = array_values(array_diff($pageIds, $withBills));
        }

        return view('admin.siswa.index', compact('students', 'stats', 'classes', 'academicYear', 'noBillIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:students,nis',
            'nisn' => 'nullable|string|max:20|unique:students,nisn',
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'status' => 'required|in:aktif,nonaktif,lulus',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'entry_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        ], [
            'nis.unique' => 'NIS sudah terdaftar, gunakan NIS lain.',
            'nisn.unique' => 'NISN sudah terdaftar, gunakan NISN lain.',
        ]);

        $room = ClassRoom::find($validated['class_id']);
        if ($room && $room->students()->count() >= (int) ($room->capacity ?? 36)) {
            return back()->withErrors(["class_id" => "Kelas {$room->name} penuh (kuota {$room->capacity})."])->withInput();
        }

        if (empty($validated['entry_year'])) {
            $validated['entry_year'] = Student::expectedEntryYear($room->grade ?? null) ?? (int) date('Y');
        }

        Student::create($validated);

        $student = Student::where('nis', $validated['nis'])->first();
        $params = [];
        if ($student && ($student->status ?? 'aktif') === 'aktif' && ! empty($this->missingBills($student))) {
            $params['open_detail'] = $student->id;
        }
        $redirect = redirect()->route('admin.siswa', $params)->with('success', 'Data siswa berhasil ditambahkan.');
        if (Student::entryMismatch((int) $validated['entry_year'], $room->grade ?? null)) {
            $redirect->with('warning', 'Tahun masuk ' . $validated['entry_year'] . ' tidak biasa untuk kelas ' . ($room->name ?? '-') . ' — periksa kembali bila bukan siswa mengulang atau pindahan.');
        }

        return $redirect;
    }

    public function bills(Student $student)
    {
        $rows = $student->bills()->with('feeCategory')->latest()->take(5)->get()
            ->map(fn ($b) => [
                'bill_code' => $b->bill_code,
                'category' => $b->feeCategory->name ?? '-',
                'amount' => number_format($b->amount, 0, ',', '.'),
                'status' => $b->status,
            ]);

        return response()->json(['rows' => $rows, 'missing' => $this->missingBills($student)]);
    }

    /** Tagihan paket semester berjalan yang belum dimiliki siswa ini. */
    protected function missingBills(Student $student): array
    {
        $ta = \App\Models\SchoolSetting::get('academic_year');
        $semester = strtolower((string) \App\Models\SchoolSetting::get('active_semester', 'Ganjil')) === 'genap' ? 'genap' : 'ganjil';
        if (! $ta) {
            return [];
        }
        $student->loadMissing('classRoom');
        $major = $student->classRoom->major ?? null;
        $grade = $student->classRoom->grade ?? null;
        $entry = $student->entry_year ? (int) $student->entry_year : null;
        $have = $student->bills()->where('academic_year', $ta)->where('semester', $semester)->pluck('fee_category_id')->all();
        $out = [];
        $cats = \App\Models\FeeCategory::where('is_active', true)
            ->whereIn('type', ['semesteran', 'sekali'])
            ->where(function ($q) use ($semester) {
                $q->where('semester', $semester)->orWhereNull('semester');
            })
            ->orderBy('sort_order')->orderBy('id')->get();
        foreach ($cats as $cat) {
            if (in_array($cat->id, $have, true)) {
                continue;
            }
            if (! \App\Support\PackageRules::appliesToCategory($cat, $major, $grade, $entry, $ta)) {
                continue;
            }
            $out[] = ['code' => $cat->code, 'name' => $cat->name, 'amount' => number_format($cat->default_amount, 0, ',', '.')];
        }

        return $out;
    }

    /** Buatkan paket tagihan semester berjalan untuk 1 siswa (yang sudah ada dilewati). */
    public function generate(Student $student)
    {
        if (($student->status ?? 'aktif') !== 'aktif') {
            return back()->with('error', 'Hanya siswa aktif yang bisa dibuatkan tagihan.');
        }
        $ta = \App\Models\SchoolSetting::get('academic_year');
        $semester = strtolower((string) \App\Models\SchoolSetting::get('active_semester', 'Ganjil')) === 'genap' ? 'genap' : 'ganjil';
        if (! $ta) {
            return back()->with('error', 'Lengkapi tahun ajaran di Pengaturan dulu.');
        }
        $student->loadMissing('classRoom');
        $major = $student->classRoom->major ?? null;
        $grade = $student->classRoom->grade ?? null;
        $entry = $student->entry_year ? (int) $student->entry_year : null;
        $cats = \App\Models\FeeCategory::where('is_active', true)
            ->whereIn('type', ['semesteran', 'sekali'])
            ->where(function ($q) use ($semester) {
                $q->where('semester', $semester)->orWhereNull('semester');
            })
            ->orderBy('sort_order')->orderBy('id')->get();
        $year = $semester === 'ganjil' ? (int) explode('/', $ta)[0] : (int) (explode('/', $ta)[1] ?? date('Y'));
        $tag = $semester === 'ganjil' ? 'GJL' : 'GNP';
        $created = 0;
        $skipped = 0;
        foreach ($cats as $cat) {
            if (! \App\Support\PackageRules::appliesToCategory($cat, $major, $grade, $entry, $ta)) {
                $skipped++;
                continue;
            }
            if ($student->bills()->where('fee_category_id', $cat->id)->where('academic_year', $ta)->where('semester', $semester)->exists()) {
                $skipped++;
                continue;
            }
            $billCode = 'INV-' . $year . '-' . $tag . '-' . substr(strtoupper(preg_replace('/[^A-Z0-9]/', '', $cat->code ?? 'CAT')), 0, 3) . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);
            $student->bills()->create([
                'bill_code' => $billCode,
                'fee_category_id' => $cat->id,
                'period_month' => $semester === 'ganjil' ? 7 : 1,
                'period_year' => $year,
                'academic_year' => $ta,
                'semester' => $semester,
                'amount' => $cat->default_amount,
                'paid_amount' => 0,
                'status' => 'unpaid',
                'due_date' => \App\Support\PackageRules::dueDateFor($cat, $ta, $semester),
                'sort_order' => \App\Support\PackageRules::sequenceIndexFor($cat->code, $semester === 'ganjil' ? 7 : 1),
            ]);
            $created++;
        }

        return back()->with('success', "Berhasil membuat {$created} tagihan untuk {$student->name} ({$skipped} dilewati karena sudah ada atau tidak berlaku).");
    }

    public function import(Request $request)
    {
        // Tahap 1: upload -> preview OK/Duplikat/Error, belum masuk DB.
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ], [
            'file.mimes' => 'File harus CSV atau Excel (.csv, .xlsx, .xls).',
        ]);

        $rows = $this->readImportRows($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.siswa')->with('error', 'File kosong atau tidak ada data mulai baris 3.');
        }

        $preview = $this->validateImportRows($rows);
        $token = \Illuminate\Support\Str::random(32);
        cache()->put('siswa_import_' . $token, $preview['valid'], now()->addMinutes(15));

        return view('admin.siswa.import-preview', [
            'rows' => $preview['rows'],
            'summary' => $preview['summary'],
            'token' => $token,
        ]);
    }

    public function importConfirm(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'mode' => 'required|in:all,valid',
        ]);

        $valid = cache()->get('siswa_import_' . $validated['token'], []);
        if (empty($valid)) {
            return redirect()->route('admin.siswa')->with('error', 'Sesi preview kedaluwarsa. Upload ulang file.');
        }

        $created = 0;
        $skipped = 0;
        foreach ($valid as $row) {
            if (Student::where('nis', $row['nis'])->exists()) {
                $skipped++;
                continue;
            }
            Student::create([
                'nis' => $row['nis'],
                'nisn' => $row['nisn'] ?: null,
                'name' => $row['name'],
                'class_id' => $row['class_id'],
                'gender' => $row['gender'],
                'status' => $row['status'],
                'guardian_name' => $row['guardian_name'] ?: null,
                'guardian_phone' => $row['guardian_phone'] ?: null,
                'address' => $row['address'] ?: null,
                'entry_year' => Student::expectedEntryYear(ClassRoom::find($row['class_id'])?->grade) ?? (int) date('Y'),
            ]);
            $created++;
        }
        cache()->forget('siswa_import_' . $validated['token']);

        return redirect()->route('admin.siswa')->with('success', "Import selesai: {$created} ditambahkan, {$skipped} dilewati (duplikat saat simpan).");
    }

    protected function readImportRows($file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (in_array($ext, ['xlsx', 'xls'], true)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getSheetByName('Template') ?? $spreadsheet->getActiveSheet();
            // Data mulai A3 ke bawah (baris 1 header, baris 2 contoh).
            $rows = [];
            for ($r = 3; $r <= $sheet->getHighestRow(); $r++) {
                $row = [];
                for ($c = 1; $c <= 9; $c++) {
                    $row[] = trim((string) $sheet->getCell([$c, $r])->getValue());
                }
                if (count(array_filter($row, fn ($v) => $v !== '')) === 0) {
                    continue;
                }
                // Lewati baris instruksi "Silakan Isi..." bila belum dihapus.
                if (stripos($row[0], 'silakan isi') !== false) {
                    continue;
                }
                $rows[] = $row;
            }

            return $rows;
        }

        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return [];
        }
        $isFirstRow = true;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count(array_filter($row, fn ($c) => trim((string) $c) !== '')) === 0) {
                continue;
            }
            if ($isFirstRow && in_array(strtolower(trim($row[0] ?? '')), ['nis', 'nomor induk', 'no induk'], true)) {
                $isFirstRow = false;
                continue;
            }
            $isFirstRow = false;
            $rows[] = array_map(fn ($c) => trim((string) $c), array_pad($row, 9, ''));
        }
        fclose($handle);

        return $rows;
    }

    protected function validateImportRows(array $rows): array
    {
        $classes = ClassRoom::pluck('id', 'name');
        $existingNis = Student::pluck('nis')->flip()->toArray();
        $seenInFile = [];
        $classCount = [];
        foreach (ClassRoom::withCount('students')->get() as $c) {
            $classCount[$c->id] = $c->students_count;
        }
        $out = [];
        $valid = [];
        $ok = $dup = $err = 0;

        foreach ($rows as $i => $row) {
            $nis = trim($row[0] ?? '');
            $nisn = trim($row[1] ?? '');
            $name = trim($row[2] ?? '');
            $kelasName = trim($row[3] ?? '');
            $gender = strtoupper(trim($row[4] ?? 'L') ?: 'L');
            $status = strtolower(trim($row[5] ?? 'aktif') ?: 'aktif');
            if ($status === 'cuti') {
                $status = 'nonaktif';
            }

            $reasons = [];
            if ($nis === '') {
                $reasons[] = 'NIS kosong';
            }
            if ($name === '') {
                $reasons[] = 'Nama kosong';
            }
            if (! isset($classes[$kelasName])) {
                $reasons[] = "Kelas '{$kelasName}' tidak cocok";
            }
            if (! in_array($gender, ['L', 'P'], true)) {
                $reasons[] = "Gender '{$gender}' harus L/P";
            }
            if (! in_array($status, ['aktif', 'nonaktif', 'lulus'], true)) {
                $reasons[] = "Status '{$status}' harus aktif/nonaktif/lulus";
            }

            $state = 'ok';
            if (! empty($reasons)) {
                $state = 'error';
                $err++;
            } elseif (isset($existingNis[$nis]) || isset($seenInFile[$nis])) {
                $state = 'duplikat';
                $reasons[] = isset($existingNis[$nis]) ? "NIS {$nis} sudah ada di sistem" : "NIS {$nis} ganda di file";
                $dup++;
            } else {
                $cid = $classes[$kelasName];
                $room = ClassRoom::find($cid);
                $used = ($classCount[$cid] ?? 0);
                if ($room && $used >= (int) ($room->capacity ?? 36)) {
                    $state = 'error';
                    $reasons[] = "Kelas '{$kelasName}' penuh (kuota {$room->capacity})";
                    $err++;
                } else {
                    $ok++;
                    $classCount[$cid] = $used + 1;
                    $valid[] = [
                        'nis' => $nis, 'nisn' => $nisn, 'name' => $name,
                        'class_id' => $cid, 'class_name' => $kelasName,
                        'gender' => $gender, 'status' => $status,
                        'guardian_name' => trim($row[6] ?? ''), 'guardian_phone' => trim($row[7] ?? ''),
                        'address' => trim($row[8] ?? ''),
                    ];
                }
            }
            $seenInFile[$nis] = true;

            $out[] = [
                'no' => $i + 1, 'nis' => $nis, 'nisn' => $nisn, 'name' => $name,
                'kelas' => $kelasName, 'gender' => $gender, 'status' => $status,
                'wali' => trim($row[6] ?? ''), 'state' => $state, 'reason' => implode('; ', $reasons) ?: '-',
            ];
        }

        return ['rows' => $out, 'valid' => $valid, 'summary' => ['ok' => $ok, 'duplikat' => $dup, 'error' => $err, 'total' => count($out)]];
    }

    public function edit(Student $student)
    {
        $student->load('classRoom');
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.siswa.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:20|unique:students,nisn,' . $student->id,
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'status' => 'required|in:aktif,nonaktif,lulus',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'entry_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        if ((int) $validated['class_id'] !== (int) $student->class_id) {
            $room = ClassRoom::find($validated['class_id']);
            if ($room && $room->students()->count() >= (int) ($room->capacity ?? 36)) {
                return back()->withErrors(["class_id" => "Kelas {$room->name} penuh (kuota {$room->capacity})."])->withInput();
            }
        }

        $student->update($validated);

        $redirect = redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui.');
        $room = ClassRoom::find($validated['class_id']);
        if (! empty($validated['entry_year']) && Student::entryMismatch((int) $validated['entry_year'], $room->grade ?? null)) {
            $redirect->with('warning', 'Tahun masuk ' . $validated['entry_year'] . ' tidak biasa untuk kelas ' . ($room->name ?? '-') . ' — periksa kembali bila bukan siswa mengulang atau pindahan.');
        }

        return $redirect;
    }

    /** Tandai massal: lulus / nonaktif. */
    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:students,id',
            'action' => 'required|in:lulus,nonaktif',
        ]);
        $n = Student::whereIn('id', $validated['ids'])->update(['status' => $validated['action'], 'updated_at' => now()]);
        $label = $validated['action'] === 'lulus' ? 'lulus' : 'nonaktif';

        return back()->with('success', "{$n} siswa ditandai {$label}.");
    }

    /** Naikkan massal ke kelas setingkat di atas (XII + tanpa tujuan dilewati). */
    public function bulkPromote(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:students,id',
        ]);
        $rooms = ClassRoom::all();
        $naik = 0;
        $lewat = [];
        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $rooms, &$naik, &$lewat) {
            foreach (Student::with('classRoom')->whereIn('id', $validated['ids'])->where('status', 'aktif')->get() as $s) {
                $t = Student::promotionTarget($s, $rooms);
                if ($t['action'] === 'naik' && $t['class']) {
                    $s->update(['class_id' => $t['class']->id]);
                    $naik++;
                } else {
                    $lewat[] = $s->name;
                }
            }
        });
        $msg = "{$naik} siswa naik kelas.";
        if (! empty($lewat)) {
            $msg .= ' Dilewati (sudah tertinggi/tanpa kelas tujuan/tidak aktif): ' . implode(', ', array_slice($lewat, 0, 5)) . (count($lewat) > 5 ? '...' : '');
        }

        return back()->with('success', $msg);
    }

    public function destroy(Student $student)
    {
        // Tahan hapus bila siswa masih punya tagihan/pembayaran —
        // batasan DB (cascade) sebenarnya mengizinkan, tapi riwayat kas
        // akan ikut hilang, jadi tolak dengan pesan yang jelas.
        if ($student->bills()->exists() || $student->payments()->exists()) {
            return redirect()->route('admin.siswa')
                ->with('error', "Siswa {$student->name} tidak dapat dihapus karena masih memiliki riwayat tagihan/pembayaran.");
        }

        $name = $student->name;
        $student->delete();

        return redirect()->route('admin.siswa')
            ->with('success', "Data siswa {$name} berhasil dihapus.");
    }
}

