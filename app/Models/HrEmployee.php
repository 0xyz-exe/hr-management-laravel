<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrEmployee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'hire_date' => 'date',
        'salary_cents' => 'integer',
        'annual_leave_days' => 'integer',
        'remaining_leave_days' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(HrPosition::class, 'hr_position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(HrAttendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(HrLeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(HrPayroll::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(HrPerformanceReview::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
