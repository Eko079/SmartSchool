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
            'cuti' => Student::where('status', 'cuti')->count(),
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
            'status' => 'required|in:aktif,cuti,lulus',
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
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.mimes' => 'File harus berformat CSV.',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (! $handle) {
            return redirect()->route('admin.siswa')->with('error', 'File CSV tidak dapat dibaca.');
        }

        $classes = ClassRoom::pluck('id', 'name');
        $created = 0;
        $skipped = 0;
        $errors = [];
        $isFirstRow = true;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // Lewati baris kosong.
            if (count(array_filter($row, fn ($c) => trim((string) $c) !== '')) === 0) {
                continue;
            }

            // Deteksi baris header (kolom pertama = "nis" / "NIS").
            if ($isFirstRow && in_array(strtolower(trim($row[0] ?? '')), ['nis', 'nomor induk', 'no induk'], true)) {
                $isFirstRow = false;
                continue;
            }
            $isFirstRow = false;

            // Format: nis, nisn, nama, kelas, gender(L/P), status, wali, wa, alamat
            $nis = trim($row[0] ?? '');
            $name = trim($row[2] ?? '');
            $kelasName = trim($row[3] ?? '');

            if ($nis === '' || $name === '' || ! isset($classes[$kelasName])) {
                $skipped++;
                $errors[] = "Baris '{$name}' dilewati: NIS/nama/kelas tidak valid.";
                continue;
            }

            if (Student::where('nis', $nis)->exists()) {
                $skipped++;
                $errors[] = "NIS {$nis} sudah ada, dilewati.";
                continue;
            }

            $gender = strtoupper(trim($row[4] ?? 'L'));
            $status = strtolower(trim($row[5] ?? 'aktif'));
            Student::create([
                'nis' => $nis,
                'nisn' => trim($row[1] ?? '') ?: null,
                'name' => $name,
                'class_id' => $classes[$kelasName],
                'gender' => in_array($gender, ['L', 'P'], true) ? $gender : 'L',
                'status' => in_array($status, ['aktif', 'cuti', 'lulus'], true) ? $status : 'aktif',
                'guardian_name' => trim($row[6] ?? '') ?: null,
                'guardian_phone' => trim($row[7] ?? '') ?: null,
                'address' => trim($row[8] ?? '') ?: null,
            ]);
            $created++;
        }
        fclose($handle);

        $msg = "Import selesai: {$created} ditambahkan, {$skipped} dilewati.";
        if ($created === 0 && $skipped > 0) {
            return redirect()->route('admin.siswa')->with('error', $msg . ' ' . implode(' ', array_slice($errors, 0, 3)));
        }

        return redirect()->route('admin.siswa')->with('success', $msg);
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
            'status' => 'required|in:aktif,cuti,lulus',
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

