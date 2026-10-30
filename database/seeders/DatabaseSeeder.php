<?php

namespace Database\Seeders;

use App\Models\HrAttendance;
use App\Models\HrDepartment;
use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use App\Models\HrPayroll;
use App\Models\HrPosition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Alexander Wright',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 1. Departments & Positions
        $deptEng = HrDepartment::firstOrCreate(['name' => 'Engineering & Technology'], ['description' => 'Platform, infrastructure, and web applications']);
        $deptProd = HrDepartment::firstOrCreate(['name' => 'Product & UX Design'], ['description' => 'Product discovery, UI/UX, and user research']);
        $deptHR = HrDepartment::firstOrCreate(['name' => 'People Operations'], ['description' => 'Talent acquisition, employee experience, and compliance']);
        $deptFin = HrDepartment::firstOrCreate(['name' => 'Finance & Legal'], ['description' => 'Corporate accounting, tax, payroll, and governance']);
        $deptSales = HrDepartment::firstOrCreate(['name' => 'Sales & Customer Growth'], ['description' => 'Enterprise sales, partnerships, and account retention']);

        $posLeadEng = HrPosition::firstOrCreate(['hr_department_id' => $deptEng->id, 'title' => 'Principal Full-Stack Engineer'], ['base_salary_cents' => 850000]);
        $posDevOps = HrPosition::firstOrCreate(['hr_department_id' => $deptEng->id, 'title' => 'Staff DevOps Architect'], ['base_salary_cents' => 820000]);
        $posSWE = HrPosition::firstOrCreate(['hr_department_id' => $deptEng->id, 'title' => 'Senior Software Engineer'], ['base_salary_cents' => 580000]);
        $posProduct = HrPosition::firstOrCreate(['hr_department_id' => $deptProd->id, 'title' => 'Senior Product Designer'], ['base_salary_cents' => 680000]);
        $posTalent = HrPosition::firstOrCreate(['hr_department_id' => $deptHR->id, 'title' => 'Talent Acquisition Specialist'], ['base_salary_cents' => 520000]);
        $posFinance = HrPosition::firstOrCreate(['hr_department_id' => $deptFin->id, 'title' => 'Financial Controller'], ['base_salary_cents' => 740000]);
        $posSales = HrPosition::firstOrCreate(['hr_department_id' => $deptSales->id, 'title' => 'Enterprise Account Executive'], ['base_salary_cents' => 650000]);

        // 2. Employees
        $empMarcus = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01021'], [
            'first_name' => 'Marcus', 'last_name' => 'Vance', 'email' => 'marcus.vance@enterprise.demo',
            'phone' => '+62 812-5551-0192', 'hr_department_id' => $deptEng->id, 'hr_position_id' => $posLeadEng->id,
            'hire_date' => '2023-02-15', 'salary_cents' => 850000, 'annual_leave_days' => 15, 'remaining_leave_days' => 12, 'status' => 'active',
        ]);

        $empElena = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01022'], [
            'first_name' => 'Elena', 'last_name' => 'Rostova', 'email' => 'elena.rostova@enterprise.demo',
            'phone' => '+62 813-5552-3341', 'hr_department_id' => $deptEng->id, 'hr_position_id' => $posDevOps->id,
            'hire_date' => '2022-08-01', 'salary_cents' => 820000, 'annual_leave_days' => 15, 'remaining_leave_days' => 14, 'status' => 'active',
        ]);

        $empSarah = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01023'], [
            'first_name' => 'Sarah', 'last_name' => 'Jenkins', 'email' => 'sarah.jenkins@enterprise.demo',
            'phone' => '+62 815-5554-7729', 'hr_department_id' => $deptProd->id, 'hr_position_id' => $posProduct->id,
            'hire_date' => '2023-05-10', 'salary_cents' => 680000, 'annual_leave_days' => 15, 'remaining_leave_days' => 9, 'status' => 'active',
        ]);

        $empKevin = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01024'], [
            'first_name' => 'Kevin', 'last_name' => 'Alamsyah', 'email' => 'kevin.alamsyah@enterprise.demo',
            'phone' => '+62 818-5555-1204', 'hr_department_id' => $deptHR->id, 'hr_position_id' => $posTalent->id,
            'hire_date' => '2024-01-15', 'salary_cents' => 520000, 'annual_leave_days' => 15, 'remaining_leave_days' => 15, 'status' => 'active',
        ]);

        $empNadia = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01025'], [
            'first_name' => 'Nadia', 'last_name' => 'Wijaya', 'email' => 'nadia.wijaya@enterprise.demo',
            'phone' => '+62 811-5556-8891', 'hr_department_id' => $deptFin->id, 'hr_position_id' => $posFinance->id,
            'hire_date' => '2023-11-01', 'salary_cents' => 740000, 'annual_leave_days' => 15, 'remaining_leave_days' => 11, 'status' => 'active',
        ]);

        $empDavid = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01026'], [
            'first_name' => 'David', 'last_name' => 'Chen', 'email' => 'david.chen@enterprise.demo',
            'phone' => '+62 811-5553-9081', 'hr_department_id' => $deptSales->id, 'hr_position_id' => $posSales->id,
            'hire_date' => '2024-03-20', 'salary_cents' => 650000, 'annual_leave_days' => 15, 'remaining_leave_days' => 15, 'status' => 'active',
        ]);

        $empRachel = HrEmployee::firstOrCreate(['employee_code' => 'EMP-01027'], [
            'first_name' => 'Rachel', 'last_name' => 'Kim', 'email' => 'rachel.kim@enterprise.demo',
            'phone' => '+62 817-5557-4412', 'hr_department_id' => $deptEng->id, 'hr_position_id' => $posSWE->id,
            'hire_date' => '2024-06-01', 'salary_cents' => 580000, 'annual_leave_days' => 15, 'remaining_leave_days' => 13, 'status' => 'active',
        ]);

        // 3. Attendances today
        $today = Carbon::now()->toDateString();
        HrAttendance::firstOrCreate(['hr_employee_id' => $empMarcus->id, 'date' => $today], ['check_in_time' => '08:45', 'check_out_time' => '17:45', 'status' => 'present', 'notes' => 'On-site HQ']);
        HrAttendance::firstOrCreate(['hr_employee_id' => $empElena->id, 'date' => $today], ['check_in_time' => '08:55', 'check_out_time' => '18:00', 'status' => 'present', 'notes' => 'On-site HQ']);
        HrAttendance::firstOrCreate(['hr_employee_id' => $empSarah->id, 'date' => $today], ['check_in_time' => '09:15', 'check_out_time' => '18:15', 'status' => 'late', 'notes' => 'Heavy traffic on Tol Dalam Kota']);
        HrAttendance::firstOrCreate(['hr_employee_id' => $empKevin->id, 'date' => $today], ['check_in_time' => '08:50', 'check_out_time' => '17:30', 'status' => 'present', 'notes' => 'Interviewing candidates']);
        HrAttendance::firstOrCreate(['hr_employee_id' => $empNadia->id, 'date' => $today], ['check_in_time' => '08:35', 'check_out_time' => '17:40', 'status' => 'present', 'notes' => 'Monthly closing audit']);
        HrAttendance::firstOrCreate(['hr_employee_id' => $empDavid->id, 'date' => $today], ['check_in_time' => '09:00', 'check_out_time' => '17:50', 'status' => 'present', 'notes' => 'Client demos']);

        // 4. Leave Requests
        HrLeaveRequest::firstOrCreate(['hr_employee_id' => $empMarcus->id, 'start_date' => Carbon::now()->addDays(3)->toDateString()], [
            'leave_type' => 'annual',
            'end_date' => Carbon::now()->addDays(5)->toDateString(),
            'days_count' => 3,
            'reason' => 'Annual family vacation trip to Bali',
            'status' => 'pending',
        ]);

        HrLeaveRequest::firstOrCreate(['hr_employee_id' => $empRachel->id, 'start_date' => Carbon::now()->addDays(7)->toDateString()], [
            'leave_type' => 'annual',
            'end_date' => Carbon::now()->addDays(8)->toDateString(),
            'days_count' => 2,
            'reason' => 'Personal home apartment relocation',
            'status' => 'pending',
        ]);

        HrLeaveRequest::firstOrCreate(['hr_employee_id' => $empSarah->id, 'start_date' => Carbon::now()->subDays(2)->toDateString()], [
            'leave_type' => 'sick',
            'end_date' => Carbon::now()->subDays(2)->toDateString(),
            'days_count' => 1,
            'reason' => 'Severe migraine and fever with clinic certificate',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approver_notes' => 'Get well soon, approved.',
        ]);

        HrLeaveRequest::firstOrCreate(['hr_employee_id' => $empElena->id, 'start_date' => Carbon::now()->addDays(14)->toDateString()], [
            'leave_type' => 'annual',
            'end_date' => Carbon::now()->addDays(15)->toDateString(),
            'days_count' => 2,
            'reason' => 'Attending CloudOps Global Summit 2026',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approver_notes' => 'Approved as professional development.',
        ]);

        // 5. Payrolls
        $month = (int) date('n');
        $year = (int) date('Y');

        HrPayroll::firstOrCreate(['hr_employee_id' => $empMarcus->id, 'period_month' => $month, 'period_year' => $year], [
            'base_salary_cents' => 850000,
            'allowances_cents' => 50000,
            'deductions_cents' => 20000,
            'net_salary_cents' => 880000,
            'payment_status' => 'paid',
            'payment_date' => Carbon::now()->subDay()->toDateString(),
            'notes' => 'Monthly executive engineering remuneration',
        ]);

        HrPayroll::firstOrCreate(['hr_employee_id' => $empElena->id, 'period_month' => $month, 'period_year' => $year], [
            'base_salary_cents' => 820000,
            'allowances_cents' => 40000,
            'deductions_cents' => 18000,
            'net_salary_cents' => 842000,
            'payment_status' => 'paid',
            'payment_date' => Carbon::now()->subDay()->toDateString(),
            'notes' => 'Infrastructure lead stipend included',
        ]);

        HrPayroll::firstOrCreate(['hr_employee_id' => $empSarah->id, 'period_month' => $month, 'period_year' => $year], [
            'base_salary_cents' => 680000,
            'allowances_cents' => 30000,
            'deductions_cents' => 15000,
            'net_salary_cents' => 695000,
            'payment_status' => 'draft',
            'notes' => 'Pending monthly design review signoff',
        ]);

        HrPayroll::firstOrCreate(['hr_employee_id' => $empKevin->id, 'period_month' => $month, 'period_year' => $year], [
            'base_salary_cents' => 520000,
            'allowances_cents' => 25000,
            'deductions_cents' => 10000,
            'net_salary_cents' => 535000,
            'payment_status' => 'draft',
            'notes' => 'Quarterly recruitment incentive pending',
        ]);

        HrPayroll::firstOrCreate(['hr_employee_id' => $empNadia->id, 'period_month' => $month, 'period_year' => $year], [
            'base_salary_cents' => 740000,
            'allowances_cents' => 45000,
            'deductions_cents' => 16000,
            'net_salary_cents' => 769000,
            'payment_status' => 'paid',
            'payment_date' => Carbon::now()->subDays(2)->toDateString(),
            'notes' => 'Finance controller regular disbursement',
        ]);
    }
}
