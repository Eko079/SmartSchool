<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'major',
        'academic_year',
        'capacity',
    ];

    /** Sisa kursi tersedia di kelas. */
    public function getSeatsLeftAttribute(): int
    {
        return max(0, (int) ($this->capacity ?? 36) - $this->students()->count());
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
