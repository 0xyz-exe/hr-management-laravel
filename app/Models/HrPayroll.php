<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrPayroll extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'base_salary_cents' => 'integer',
        'allowances_cents' => 'integer',
        'deductions_cents' => 'integer',
        'net_salary_cents' => 'integer',
        'payment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'hr_employee_id');
    }
}
