<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\Request;

class KenaikanController extends Controller
{
    public function index(Request $request)
    {
        $current = (string) (SchoolSetting::get('academic_year') ?: (date('Y') . '/' . (date('Y') + 1)));
        $cy = (int) explode('/', $current)[0];
        $options = [$current, $cy . '/' . ($cy + 1), ($cy + 1) . '/' . ($cy + 2)];
        $target = (string) $request->input('academic_year', $cy . '/' . ($cy + 1));
        if (! in_array($target, $options, true)) {
            $target = $options[1];
        }

        $rooms = ClassRoom::all();
        $doneIds = json_decode((string) (SchoolSetting::get('kenaikan_ids_' . $target) ?? '[]'), true) ?: [];
        $doneAt = SchoolSetting::get('kenaikan_' . $target);

        $groups = ['naik' => [], 'lulus' => [], 'tanpa_kelas' => []];
        foreach (Student::with('classRoom')->where('status', 'aktif')->orderBy('name')->get() as $s) {
            $t = $this->targetFor($s, $rooms);
            $row = ['student' => $s, 'target' => $t['class'], 'done' => in_array($s->id, $doneIds, true)];
            if ($t['action'] === 'lulus') {
                $groups['lulus'][] = $row;
            } elseif ($t['action'] === 'naik' && $t['class']) {
                $groups['naik'][] = $row;
            } else {
                $groups['tanpa_kelas'][] = $row;
            }
        }

        return view('admin.kenaikan.index', compact('options', 'target', 'current', 'groups', 'doneAt'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:9',
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
            'force' => 'nullable|boolean',
        ]);
        $ta = $validated['academic_year'];
        $doneIds = json_decode((string) (SchoolSetting::get('kenaikan_ids_' . $ta) ?? '[]'), true) ?: [];
        if (SchoolSetting::get('kenaikan_' . $ta) && empty($validated['force']) && empty($validated['students'])) {
            return back()->with('error', 'Kenaikan ' . $ta . ' sudah pernah diproses. Centang "Jalankan lagi" untuk yang belum diproses.');
        }

        $rooms = ClassRoom::all();
        $ids = array_diff($validated['students'] ?? [], $doneIds);
        $naik = 0;
        $lulus = 0;
        $lewat = 0;
        $baru = $doneIds;
        \Illuminate\Support\Facades\DB::transaction(function () use ($ids, $rooms, &$naik, &$lulus, &$lewat, &$baru) {
            foreach (Student::with('classRoom')->whereIn('id', $ids)->where('status', 'aktif')->get() as $s) {
                $t = $this->targetFor($s, $rooms);
                if ($t['action'] === 'lulus') {
                    $s->update(['status' => 'lulus']);
                    $lulus++;
                    $baru[] = $s->id;
                } elseif ($t['action'] === 'naik' && $t['class']) {
                    $s->update(['class_id' => $t['class']->id]);
                    $naik++;
                    $baru[] = $s->id;
                } else {
                    $lewat++;
                }
            }
        });
        SchoolSetting::set('kenaikan_' . $ta, now()->toDateTimeString());
        SchoolSetting::set('kenaikan_ids_' . $ta, json_encode(array_values(array_unique($baru))));

        return redirect()->route('admin.kenaikan', ['academic_year' => $ta])
            ->with('success', "Kenaikan {$ta} selesai: {$naik} naik kelas, {$lulus} lulus, {$lewat} dilewati (tanpa kelas tujuan). Lanjutkan generate paket tahun ajaran baru di Billing.");
    }

    /** Tujuan dari kelas saat ini: naik (kelas setingkat+1, jurusan & nomor sama) / lulus / tetap. */
    protected function targetFor(Student $student, $rooms): array
    {
        $name = $student->classRoom->name ?? '';
        if (! preg_match('/^(X|XI|XII)\s+(.+?)\s+(\d+)$/', $name, $m)) {
            return ['action' => 'tetap', 'class' => null];
        }
        [, $grade, $major, $no] = $m;
        if ($grade === 'XII') {
            return ['action' => 'lulus', 'class' => null];
        }
        $next = ['X' => 'XI', 'XI' => 'XII'][$grade];
        $found = $rooms->first(fn ($c) => $c->grade === $next && $c->major === $major && (int) substr(strrchr($c->name, ' '), 1) === (int) $no);

        return ['action' => 'naik', 'class' => $found];
    }
}
