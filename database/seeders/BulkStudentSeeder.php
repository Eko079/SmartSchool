<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BulkStudentSeeder extends Seeder
{
    public function run(): void
    {
        $male = ['Agus','Andi','Arif','Bagus','Bambang','Bayu','Bimo','Budi','Dadan','Danang','Dedi','Dimas','Eko','Fajar','Farhan','Fikri','Galih','Gilang','Hadi','Hendra','Herman','Ilham','Irvan','Joko','Kevin','Krisna','Lukman','Made','Nanda','Nugroho','Pandu','Putra','Raka','Rama','Rangga','Reza','Rian','Rizky','Rudi','Samsul','Sandi','Satria','Taufik','Teguh','Wahyu','Wisnu','Yoga','Yuda','Yusuf','Zaenal','Aldi','Daffa','Fauzan','Hafiz','Irfan','Rafi'];
        $female = ['Ayu','Anisa','Annisa','Aulia','Ayu','Bella','Citra','Dewi','Dian','Dinda','Fitri','Gita','Hana','Indah','Intan','Kirana','Laras','Lestari','Lina','Maya','Melati','Nadia','Naila','Nita','Novia','Nur','Putri','Rani','Ratna','Rina','Rizka','Salsa','Sari','Sinta','Siti','Tania','Tiara','Utari','Vina','Wulan','Yuni','Zahra','Amelia','Aurel','Chelsea','Dinda','Farah','Gisella','Jesica','Kartika','Livia','Maura','Nabila','Olivia','Prilly','Queena','Salma'];
        $last = ['Saputra','Pratama','Kusuma','Wijaya','Santoso','Nugroho','Setiawan','Hidayat','Ramadhan','Maulana','Firmansyah','Gunawan','Hartono','Siregar','Nasution','Pangaribuan','Simbolon','Sitompul','Wibowo','Susanto','Pramono','Handoko','Setiawan','Laksmana','Aditya','Nugraha','Permana','Saputra','Utama','Wicaksono','Kurniawan','Hakim','Syahputra','Alamsyah','Fahlevi','Hamdani','Irwansyah','Juliyanto','Kurnia','Lubis','Maharani','Napitupulu','Octavia','Pangestu','Qolbi','Rahmawati','Salsabila','Tanjung','Utami','Wulandari','Yolanda','Zulaikha','Anggraini','Maharani','Puspita','Rahayu','Sari','Dewi','Ningsih','Aisyah','Halim','Sanjaya','Pramudya','Firmanto','Ginanjar','Herlambang','Irawan','Jaya','Kahfi','Lesmana'];
        $guardians = ['Sutrisno','Hartono','Suharto','Wagiman','Paino','Karto','Darmo','Slamet','Agus','Joko','Rudi','Dedi','Hendra','Budi','Ahmad','Muhammad','Abdul','Haji','Udin','Maman','Asep','Dadang','Yayat','Cecep','Entis','Tono','Jajang','Dede','Ibu Ratna','Ibu Sari','Ibu Dewi','Ibu Maya','Ibu Rina','Ibu Yuni','Ibu Fitri','Ibu Indah','Ibu Wulan','Bapak Andi','Bapak Budi','Bapak Harto','Bapak Seno','Bapak Tanto','Bapak Wawan','Bapak Yanto','Bapak Dodi','Bapak Eko','Bapak Fajar','Bapak Gani','Bapak Heru','Bapak Iman'];
        $streets = ['Pendidikan','Merdeka','Pahlawan','Diponegoro','Gajah Mada','Sudirman','Thamrin','Asia Afrika','Pemuda','Veteran'];

        // Kombinasi nama unik: acak tanpa pengulangan.
        $combos = [];
        foreach ($male as $f) {
            foreach ($last as $l) {
                $combos[] = ['name' => "$f $l", 'gender' => 'L'];
            }
        }
        foreach ($female as $f) {
            foreach ($last as $l) {
                $combos[] = ['name' => "$f $l", 'gender' => 'P'];
            }
        }
        shuffle($combos);
        $used = Student::pluck('name')->all();
        $combos = array_values(array_filter($combos, fn ($c) => ! in_array($c['name'], $used, true)));

        $entryByGrade = ['X' => 2026, 'XI' => 2025, 'XII' => 2024];
        $nisSeq = 1013;
        $nisnSeq = 13;
        $made = 0;
        $wi = 0;

        foreach (ClassRoom::withCount('students')->orderBy('id')->get() as $cls) {
            $target = random_int(24, 30);
            $need = max(0, $target - $cls->students_count);
            for ($i = 0; $i < $need; $i++) {
                if (empty($combos)) {
                    $this->command->error('Kombinasi nama habis!');
                    return;
                }
                $pick = array_pop($combos);
                $nis = '2026' . str_pad($nisSeq++, 4, '0', STR_PAD_LEFT);
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $pick['name']));
                $g = $guardians[array_rand($guardians)];
                $ph = '08' . (string) random_int(10, 99) . '-' . (string) random_int(1000, 9999) . '-' . (string) random_int(1000, 9999);
                $st = Student::create([
                    'class_id' => $cls->id,
                    'nis' => $nis,
                    'nisn' => '0081234' . str_pad($nisnSeq++, 3, '0', STR_PAD_LEFT),
                    'name' => $pick['name'],
                    'email' => $slug . '.' . $nis . '@smartschool.id',
                    'entry_year' => $entryByGrade[$cls->grade] ?? 2026,
                    'gender' => $pick['gender'],
                    'status' => 'aktif',
                    'guardian_name' => $g,
                    'guardian_phone' => $ph,
                    'address' => 'Jl. ' . $streets[array_rand($streets)] . ' No. ' . random_int(1, 200) . ', Jakarta Selatan',
                ]);
                User::create([
                    'name' => $g,
                    'email' => 'wali-' . $nis . '@smartschool.id',
                    'password' => Hash::make('wali123'),
                    'role' => 'wali',
                    'student_id' => $st->id,
                    'phone' => $ph,
                    'failed_attempts' => 0,
                    'locked_until' => null,
                    'notify_wa' => true,
                    'notify_email' => true,
                ]);
                $made++;
            }
            $wi++;
            if ($wi % 6 === 0) {
                $this->command->info("... $wi kelas, $made siswa baru");
            }
        }
        $this->command->info("Selesai: $made siswa + akun wali baru.");
    }
}
