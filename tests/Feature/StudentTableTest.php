<?php

namespace Tests\Feature;

use App\Models\Student;
use Tests\TestCase;

class StudentTableTest extends TestCase
{
    public function test_sorting_params_render(): void
    {
        foreach (['nis', 'name', 'class', 'status'] as $sort) {
            foreach (['asc', 'desc'] as $dir) {
                $res = $this->get(route('admin.siswa', ['sort' => $sort, 'dir' => $dir]));
                $res->assertStatus(200);
            }
        }
    }

    public function test_invalid_sort_falls_back_safely(): void
    {
        $res = $this->get(route('admin.siswa', ['sort' => 'injection;DROP', 'dir' => 'asc']));
        $res->assertStatus(200);
    }

    public function test_edit_page_renders(): void
    {
        $st = Student::first();
        $res = $this->get(route('admin.siswa.edit', $st->id));
        $res->assertStatus(200);
        $res->assertSee($st->name);
    }

    public function test_bills_json_endpoint(): void
    {
        $st = Student::has('bills')->first();
        $res = $this->getJson(route('admin.siswa.bills', $st->id));
        $res->assertStatus(200);
        $res->assertJsonStructure([['bill_code', 'category', 'amount', 'status']]);
    }

    public function test_protected_delete_refused_when_has_bills(): void
    {
        $st = Student::has('bills')->first();
        $res = $this->delete(route('admin.siswa.destroy', $st->id));
        $res->assertRedirect(route('admin.siswa'));
        $res->assertSessionHas('error');
        $this->assertDatabaseHas('students', ['id' => $st->id]);
    }

    public function test_duplicate_nis_rejected(): void
    {
        $existing = Student::first();
        $res = $this->post(route('admin.siswa.store'), [
            'name' => 'Duplikat NIS',
            'nis' => $existing->nis,
            'class_id' => $existing->class_id,
            'gender' => 'L',
            'status' => 'aktif',
        ]);
        $res->assertSessionHasErrors('nis');
        $this->assertDatabaseMissing('students', ['name' => 'Duplikat NIS']);
    }

    public function test_import_rejects_non_csv(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 't') . '.php';
        file_put_contents($tmp, '<?php echo 1;');
        $res = $this->post(route('admin.siswa.import'), [
            'file' => new \Illuminate\Http\UploadedFile($tmp, 'x.php', 'text/php', null, true),
        ]);
        $res->assertSessionHasErrors('file');
        @unlink($tmp);
    }
}
