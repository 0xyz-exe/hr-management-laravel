<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrPosition extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'base_salary_cents' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(HrEmployee::class);
    }
}
