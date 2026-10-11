<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'nis',
        'nisn',
        'name',
        'email',
        'entry_year',
        'photo_path',
        'birthdate',
        'gender',
        'status',
        'guardian_name',
        'guardian_phone',
        'address',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /** Tahun masuk wajar untuk tingkat ini (X=awal TA, XI=setahun lalu, XII=2 thn lalu). */
    public static function expectedEntryYear(?string $grade, ?string $academicYear = null): ?int
    {
        $academicYear ??= \App\Models\SchoolSetting::get('academic_year');
        if (! $grade || ! $academicYear || ! str_contains($academicYear, '/')) {
            return null;
        }
        $idx = ['X' => 0, 'XI' => 1, 'XII' => 2][$grade] ?? null;
        if ($idx === null) {
            return null;
        }

        return (int) explode('/', $academicYear)[0] - $idx;
    }

    /** true bila tahun masuk tidak biasa untuk tingkatnya (mis. bukan siswa baru). */
    public static function entryMismatch(?int $entryYear, ?string $grade, ?string $academicYear = null): bool
    {
        $exp = self::expectedEntryYear($grade, $academicYear);
        if ($exp === null || $entryYear === null) {
            return false;
        }

        return (int) $entryYear !== $exp;
    }

    /** Tujuan kenaikan dari kelas saat ini: naik (kelas setingkat+1, jurusan & nomor sama) / lulus / tetap. */
    public static function promotionTarget(self $student, $rooms): array
    {
        $name = $student->classRoom->name ?? '';
        if (! preg_match('/^(X|XI|XII)\s+(.+?)\s+(\d+)$/', $name, $m)) {
            return ['action' => 'tetap', 'class' => null];
        }
        [, $grade, $major, $no] = $m;
        if ($grade === 'XII') {
            return ['action' => 'lulus', 'class' => null];
        }
        $next = ['X' => 'XI', 'XI' => 'XII'][$grade] ?? null;
        $found = $next ? $rooms->first(fn ($c) => $c->grade === $next && $c->major === $major && (int) substr(strrchr($c->name, ' '), 1) === (int) $no) : null;

        return $found ? ['action' => 'naik', 'class' => $found] : ['action' => 'tetap', 'class' => null];
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function portalUsers()
    {
        return $this->hasMany(User::class, 'student_id');
    }
}
