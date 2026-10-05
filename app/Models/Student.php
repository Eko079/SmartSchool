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
