<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::withCount('students')->orderBy('grade')->orderBy('major')->orderBy('name')->get();

        return view('admin.kelas.index', compact('classes'));
    }

    private function rules(?ClassRoom $room = null): array
    {
        return [
            // Format dikunci agar pemetaan kenaikan kelas tetap jalan (mis. X IPA 1).
            'name' => [
                'required', 'string', 'max:50',
                'regex:/^(X|XI|XII)\s+.+\s+\d+$/',
                Rule::unique('classes', 'name')->ignore($room?->id),
            ],
            'major' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:1|max:100',
        ];
    }

    private function messages(): array
    {
        return ['name.regex' => 'Format nama: Tingkat Jurusan Nomor, contoh X IPA 1.'];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());
        $room = ClassRoom::create([
            'name' => $validated['name'],
            'grade' => explode(' ', $validated['name'])[0],
            'major' => $validated['major'],
            'capacity' => $validated['capacity'] ?? null,
        ]);

        return back()->with('success', "Kelas {$room->name} ditambahkan.");
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate($this->rules($class), $this->messages());
        $class->update([
            'name' => $validated['name'],
            'grade' => explode(' ', $validated['name'])[0],
            'major' => $validated['major'],
            'capacity' => $validated['capacity'] ?? null,
        ]);

        return back()->with('success', "Kelas {$class->name} diperbarui.");
    }

    public function destroy(ClassRoom $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', "Kelas {$class->name} masih berisi siswa, tidak bisa dihapus. Pindahkan siswanya dulu.");
        }
        $class->delete();

        return back()->with('success', "Kelas {$class->name} dihapus.");
    }
}
