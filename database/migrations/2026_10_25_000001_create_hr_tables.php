<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_department_id')->constrained('hr_departments')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedBigInteger('base_salary_cents')->default(0);
            $table->timestamps();
        });

        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hr_department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->foreignId('hr_position_id')->nullable()->constrained('hr_positions')->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default('active'); // active, on_leave, terminated
            $table->date('hire_date');
            $table->unsignedBigInteger('salary_cents')->default(0);
            $table->integer('annual_leave_days')->default(15);
            $table->integer('remaining_leave_days')->default(15);
            $table->timestamps();
        });

        Schema::create('hr_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('status')->default('present'); // present, late, absent, half_day
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['hr_employee_id', 'date']);
        });

        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('leave_type')->default('annual'); // annual, sick, maternity, unpaid
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->default(1);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approver_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedBigInteger('base_salary_cents');
            $table->unsignedBigInteger('allowances_cents')->default(0);
            $table->unsignedBigInteger('deductions_cents')->default(0);
            $table->unsignedBigInteger('net_salary_cents');
            $table->string('payment_status')->default('draft'); // draft, processed, paid
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['hr_employee_id', 'period_month', 'period_year']);
        });

        Schema::create('hr_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('review_period'); // e.g. Q1 2026, Annual 2026
            $table->unsignedTinyInteger('rating')->default(5); // 1-5
            $table->text('feedback');
            $table->text('goals')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_holidays');
        Schema::dropIfExists('hr_performance_reviews');
        Schema::dropIfExists('hr_payrolls');
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_attendances');
        Schema::dropIfExists('hr_employees');
        Schema::dropIfExists('hr_positions');
        Schema::dropIfExists('hr_departments');
    }
};
