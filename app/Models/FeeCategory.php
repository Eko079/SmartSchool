<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'default_amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_amount' => 'decimal:2',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'fee_category_id');
    }
}
