<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ClassRoom::pluck('id', 'name');

        $students = [
            ['name' => 'Muhammad Fajar', 'nis' => '20261001', 'nisn' => '0081234001', 'class' => 'X RPL 1', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Bambang Pamungkas', 'guardian_phone' => '0812-3456-7890'],
            ['name' => 'Aulia Rahma', 'nis' => '20261002', 'nisn' => '0081234002', 'class' => 'XI RPL 1', 'gender' => 'P', 'status' => 'aktif', 'guardian_name' => 'Hendra Gunawan', 'guardian_phone' => '0813-9876-5432'],
            ['name' => 'Budi Santoso', 'nis' => '20261003', 'nisn' => '0081234003', 'class' => 'X RPL 2', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Agus Setiawan', 'guardian_phone' => '0821-1122-3344'],
            ['name' => 'Siti Nurhaliza', 'nis' => '20261004', 'nisn' => '0081234004', 'class' => 'XII RPL 1', 'gender' => 'P', 'status' => 'aktif', 'guardian_name' => 'Joko Widodo', 'guardian_phone' => '0852-5566-7788'],
            ['name' => 'Dimas Anggara', 'nis' => '20261005', 'nisn' => '0081234005', 'class' => 'XI TKJ 1', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Rudi Hartono', 'guardian_phone' => '0878-9900-1122'],
            ['name' => 'Rina Kartika', 'nis' => '20261006', 'nisn' => '0081234006', 'class' => 'X TKJ 1', 'gender' => 'P', 'status' => 'cuti', 'guardian_name' => 'Slamet Riyadi', 'guardian_phone' => '0899-2233-4455'],
            ['name' => 'Farhan Maulana', 'nis' => '20261007', 'nisn' => '0081234007', 'class' => 'XII MM 1', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Ahmad Dahlan', 'guardian_phone' => '0812-8877-6655'],
            ['name' => 'Annisa Putri', 'nis' => '20261008', 'nisn' => '0081234008', 'class' => 'XI RPL 2', 'gender' => 'P', 'status' => 'aktif', 'guardian_name' => 'Tri Sutrisno', 'guardian_phone' => '0813-4455-6677'],
            ['name' => 'Kevin Sanjaya', 'nis' => '20261009', 'nisn' => '0081234009', 'class' => 'X RPL 1', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Susi Susanti', 'guardian_phone' => '0822-1133-5577'],
            ['name' => 'Dewi Sartika', 'nis' => '20261010', 'nisn' => '0081234010', 'class' => 'XII RPL 1', 'gender' => 'P', 'status' => 'lulus', 'guardian_name' => 'Raden Saleh', 'guardian_phone' => '0838-9988-7766'],
            ['name' => 'Bagus Wicaksono', 'nis' => '20261011', 'nisn' => '0081234011', 'class' => 'XI TKJ 1', 'gender' => 'L', 'status' => 'aktif', 'guardian_name' => 'Gito Rollies', 'guardian_phone' => '0857-4433-2211'],
            ['name' => 'Zahra Maharani', 'nis' => '20261012', 'nisn' => '0081234012', 'class' => 'X RPL 2', 'gender' => 'P', 'status' => 'aktif', 'guardian_name' => 'Maya Estianty', 'guardian_phone' => '0877-6655-4433'],
        ];

        foreach ($students as $idx => $s) {
            Student::updateOrCreate(
                ['nis' => $s['nis']],
                [
                    'class_id' => $classes[$s['class']] ?? 1,
                    'nisn' => $s['nisn'],
                    'name' => $s['name'],
                    'gender' => $s['gender'],
                    'status' => $s['status'],
                    'guardian_name' => $s['guardian_name'],
                    'guardian_phone' => $s['guardian_phone'],
                    'address' => 'Jl. Pendidikan No. ' . ($idx + 10) . ', Jakarta Selatan',
                ]
            );
        }
    }
}

