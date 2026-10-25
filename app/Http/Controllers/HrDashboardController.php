<?php

namespace App\Http\Controllers;

use App\Models\HrAttendance;
use App\Models\HrDepartment;
use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use App\Models\HrPayroll;
use App\Models\HrPosition;
use Illuminate\View\View;

class HrDashboardController extends Controller
{
    public function index(): View
    {
        // Seeding baseline departments if empty
        if (HrDepartment::count() === 0) {
            $eng = HrDepartment::create(['name' => 'Engineering', 'description' => 'Software and Hardware']);
            HrPosition::create(['hr_department_id' => $eng->id, 'title' => 'Software Engineer', 'base_salary_cents' => 600000]);
            HrPosition::create(['hr_department_id' => $eng->id, 'title' => 'QA Engineer', 'base_salary_cents' => 450000]);

            $sales = HrDepartment::create(['name' => 'Sales & Marketing', 'description' => 'Commercial Growth']);
            HrPosition::create(['hr_department_id' => $sales->id, 'title' => 'Account Executive', 'base_salary_cents' => 500000]);
        }

        $activeEmployees = HrEmployee::where('status', 'active')->count();
        $todayAttendances = HrAttendance::where('date', now()->toDateString())->count();
        $pendingLeaves = HrLeaveRequest::where('status', 'pending')->with('employee')->latest()->get();
        $totalMonthlyPayroll = (int) HrEmployee::where('status', 'active')->sum('salary_cents');

        $metrics = [
            'total_active_headcount' => $activeEmployees,
            'today_checkins' => $todayAttendances,
            'pending_leaves_count' => $pendingLeaves->count(),
            'monthly_payroll_estimate_cents' => $totalMonthlyPayroll,
        ];

        $departments = HrDepartment::withCount('employees')->get();
        $recentEmployees = HrEmployee::with(['department', 'position'])->latest()->take(5)->get();

        return view('dashboard', compact('metrics', 'pendingLeaves', 'departments', 'recentEmployees'));
    }
}
