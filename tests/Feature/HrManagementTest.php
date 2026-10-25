<?php

namespace Tests\Feature;

use App\Models\HrAttendance;
use App\Models\HrDepartment;
use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use App\Models\HrPayroll;
use App\Models\HrPosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setupOrg(): array
    {
        $dept = HrDepartment::create(['name' => 'Technology']);
        $pos = HrPosition::create(['hr_department_id' => $dept->id, 'title' => 'Software Engineer']);
        return [$dept, $pos];
    }

    public function test_hr_dashboard_renders_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Human Resources Management Dashboard');
    }

    public function test_employee_creation_and_csv_export(): void
    {
        [$dept, $pos] = $this->setupOrg();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/employees', [
            'first_name' => 'David',
            'last_name' => 'Miller',
            'email' => 'david.m@enterprise.com',
            'phone' => '+15559876',
            'hr_department_id' => $dept->id,
            'hr_position_id' => $pos->id,
            'hire_date' => now()->toDateString(),
            'salary' => 8500.00,
            'annual_leave_days' => 18,
        ]);

        $response->assertRedirect('/employees');
        $this->assertDatabaseHas('hr_employees', [
            'first_name' => 'David',
            'last_name' => 'Miller',
            'salary_cents' => 850000,
            'remaining_leave_days' => 18,
        ]);

        // CSV export
        $export = $this->actingAs($user)->get('/employees/export/csv');
        $export->assertStatus(200);
        $export->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('David Miller', $export->streamedContent());
    }

    public function test_attendance_recording(): void
    {
        [$dept, $pos] = $this->setupOrg();
        $user = User::factory()->create();

        $employee = HrEmployee::create([
            'employee_code' => 'EMP-TEST-01',
            'first_name' => 'Emma',
            'last_name' => 'Watson',
            'email' => 'emma@enterprise.com',
            'hr_department_id' => $dept->id,
            'hr_position_id' => $pos->id,
            'hire_date' => now()->toDateString(),
            'salary_cents' => 600000,
        ]);

        $today = now()->toDateString();
        $response = $this->actingAs($user)->post('/attendance', [
            'hr_employee_id' => $employee->id,
            'date' => $today,
            'status' => 'present',
            'check_in_time' => '08:55',
            'check_out_time' => '17:30',
        ]);

        $response->assertRedirect('/attendance?date=' . $today);
        $this->assertDatabaseHas('hr_attendances', [
            'hr_employee_id' => $employee->id,
            'status' => 'present',
            'check_in_time' => '08:55',
        ]);
    }

    public function test_leave_request_submission_and_approval_balance_deduction(): void
    {
        [$dept, $pos] = $this->setupOrg();
        $user = User::factory()->create();

        $employee = HrEmployee::create([
            'employee_code' => 'EMP-TEST-02',
            'first_name' => 'Robert',
            'last_name' => 'Plant',
            'email' => 'robert@enterprise.com',
            'hr_department_id' => $dept->id,
            'hr_position_id' => $pos->id,
            'hire_date' => now()->toDateString(),
            'salary_cents' => 500000,
            'annual_leave_days' => 15,
            'remaining_leave_days' => 15,
        ]);

        // Request 5 days
        $response = $this->actingAs($user)->post('/leaves', [
            'hr_employee_id' => $employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'days_count' => 5,
            'reason' => 'Family vacation',
        ]);

        $response->assertRedirect('/leaves');
        $leave = HrLeaveRequest::where('hr_employee_id', $employee->id)->firstOrFail();
        $this->assertEquals('pending', $leave->status);

        // Approve leave
        $approve = $this->actingAs($user)->post("/leaves/{$leave->id}/process", [
            'status' => 'approved',
            'approver_notes' => 'Enjoy your leave!',
        ]);

        $approve->assertRedirect('/leaves');
        $leave->refresh();
        $employee->refresh();
        $this->assertEquals('approved', $leave->status);
        $this->assertEquals(10, $employee->remaining_leave_days); // 15 - 5 = 10
    }

    public function test_payroll_generation_and_payment(): void
    {
        [$dept, $pos] = $this->setupOrg();
        $user = User::factory()->create();

        $employee = HrEmployee::create([
            'employee_code' => 'EMP-TEST-03',
            'first_name' => 'Carlos',
            'last_name' => 'Santana',
            'email' => 'carlos@enterprise.com',
            'hr_department_id' => $dept->id,
            'hr_position_id' => $pos->id,
            'hire_date' => now()->toDateString(),
            'salary_cents' => 600000, // $6,000.00
        ]);

        // Base 6000 + 500 allowance - 200 deduction = 6300 net
        $response = $this->actingAs($user)->post('/payroll/generate', [
            'hr_employee_id' => $employee->id,
            'period_month' => 10,
            'period_year' => 2026,
            'allowances' => 500.00,
            'deductions' => 200.00,
            'notes' => 'Standard monthly run',
        ]);

        $response->assertSessionHas('success');
        $payroll = HrPayroll::where('hr_employee_id', $employee->id)->firstOrFail();
        $this->assertEquals(600000, $payroll->base_salary_cents);
        $this->assertEquals(50000, $payroll->allowances_cents);
        $this->assertEquals(20000, $payroll->deductions_cents);
        $this->assertEquals(630000, $payroll->net_salary_cents);
        $this->assertEquals('draft', $payroll->payment_status);

        // Mark paid
        $paidResponse = $this->actingAs($user)->post("/payroll/{$payroll->id}/paid");
        $paidResponse->assertSessionHas('success');
        $payroll->refresh();
        $this->assertEquals('paid', $payroll->payment_status);
        $this->assertNotNull($payroll->payment_date);
    }
}
