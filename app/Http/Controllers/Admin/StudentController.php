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

        return view('admin.siswa.index', compact('students', 'stats', 'classes', 'academicYear'));
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
        ], [
            'nis.unique' => 'NIS sudah terdaftar, gunakan NIS lain.',
            'nisn.unique' => 'NISN sudah terdaftar, gunakan NISN lain.',
        ]);

        Student::create($validated);

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil ditambahkan.');
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

        return response()->json($rows);
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
                $ok++;
                $valid[] = [
                    'nis' => $nis, 'nisn' => $nisn, 'name' => $name,
                    'class_id' => $classes[$kelasName], 'class_name' => $kelasName,
                    'gender' => $gender, 'status' => $status,
                    'guardian_name' => trim($row[6] ?? ''), 'guardian_phone' => trim($row[7] ?? ''),
                    'address' => trim($row[8] ?? ''),
                ];
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
        ]);

        $student->update($validated);

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui.');
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

