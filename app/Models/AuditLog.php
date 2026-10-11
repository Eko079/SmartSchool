<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'aksi', 'detail', 'ip'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Catat jejak. Aman dipanggil tanpa login (user_id null). */
    public static function log(string $aksi, ?string $detail = null): void
    {
        try {
            static::create([
                'user_id' => Auth::id(),
                'aksi' => $aksi,
                'detail' => $detail,
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Audit tidak boleh menggagalkan aksi utama.
        }
    }

    /** Label aksi untuk tampilan (tanpa istilah teknis). */
    public static function label(string $aksi): string
    {
        return match ($aksi) {
            'kategori.tambah' => 'Tambah kategori',
            'kategori.ubah' => 'Ubah kategori',
            'kategori.status' => 'Aktif/nonaktif kategori',
            'siswa.hapus' => 'Hapus siswa',
            'siswa.massal' => 'Aksi massal siswa',
            'siswa.generate' => 'Buat tagihan siswa',
            'siswa.import' => 'Import siswa',
            'billing.generate' => 'Generate tagihan',
            'billing.paket' => 'Generate paket',
            'billing.denda' => 'Hitung denda',
            'akun.tambah' => 'Tambah akun',
            'akun.reset' => 'Reset sandi',
            'akun.status' => 'Aktif/nonaktif akun',
            'pengaturan.ubah' => 'Ubah pengaturan',
            'kelas.tambah' => 'Tambah kelas',
            'kelas.ubah' => 'Ubah kelas',
            'kelas.hapus' => 'Hapus kelas',
            default => $aksi,
        };
    }
}
