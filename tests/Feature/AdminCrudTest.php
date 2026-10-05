<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\FeeCategory;
use App\Models\Student;
use App\Models\SchoolSetting;
use App\Models\User;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::first() ?? User::factory()->create());
    }

    public function test_can_create_and_delete_student(): void
    {
        $class = ClassRoom::first();

        $studentData = [
            'nis' => 'TEST' . rand(1000, 9999),
            'nisn' => 'NISN' . rand(1000, 9999),
            'name' => 'Testing Student',
            'class_id' => $class->id,
            'gender' => 'L',
            'status' => 'aktif',
            'guardian_name' => 'Wali Test',
            'guardian_phone' => '081299990000',
        ];

        $response = $this->post(route('admin.siswa.store'), $studentData);
        $response->assertRedirect(route('admin.siswa'));

        $this->assertDatabaseHas('students', [
            'nis' => $studentData['nis'],
            'name' => 'Testing Student',
        ]);

        $student = Student::where('nis', $studentData['nis'])->first();

        // Delete
        $delResponse = $this->delete(route('admin.siswa.destroy', $student->id));
        $delResponse->assertRedirect(route('admin.siswa'));

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    public function test_can_create_and_toggle_fee_category(): void
    {
        $code = 'TEST-' . rand(100, 999);
        $categoryData = [
            'code' => $code,
            'name' => 'Biaya Praktikum Khusus',
            'type' => 'sekali',
            'default_amount' => 500000,
            'description' => 'Test kategori tagihan',
        ];

        $response = $this->post(route('admin.kategori-tagihan.store'), $categoryData);
        $response->assertRedirect(route('admin.kategori-tagihan'));

        $this->assertDatabaseHas('fee_categories', [
            'code' => $code,
        ]);

        $category = FeeCategory::where('code', $code)->first();
        $initialStatus = $category->is_active;

        $toggleResponse = $this->post(route('admin.kategori-tagihan.toggle', $category->id));
        $toggleResponse->assertRedirect(route('admin.kategori-tagihan'));

        $category->refresh();
        $this->assertNotEquals($initialStatus, $category->is_active);
    }

    public function test_can_generate_billing(): void
    {
        $class = ClassRoom::first();
        $category = FeeCategory::first();

        $postData = [
            'fee_category_id' => $category->id,
            'period_month' => 11,
            'period_year' => 2026,
            'amount' => 750000,
            'due_date' => '2026-11-10',
            'classes' => [$class->id],
        ];

        $response = $this->post(route('admin.billing.generate'), $postData);
        $response->assertRedirect(route('admin.billing'));
    }

    public function test_can_update_settings(): void
    {
        $payload = [
            'school_name' => 'SMK SmartSchool Unggulan',
            'npsn' => '20199999',
            'academic_year' => '2026/2027',
        ];

        $response = $this->post(route('admin.pengaturan.update'), $payload);
        $response->assertRedirect(route('admin.pengaturan'));

        $this->assertEquals('SMK SmartSchool Unggulan', SchoolSetting::get('school_name'));
        $this->assertEquals('20199999', SchoolSetting::get('npsn'));
    }
}
