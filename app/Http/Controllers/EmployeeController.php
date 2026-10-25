<?php

namespace App\Http\Controllers;

use App\Models\HrDepartment;
use App\Models\HrEmployee;
use App\Models\HrPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = HrEmployee::with(['department', 'position'])->latest();

        if ($dept = $request->input('department_id')) {
            $query->where('hr_department_id', $dept);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(15);
        $departments = HrDepartment::orderBy('name')->get();
        $positions = HrPosition::orderBy('title')->get();

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:hr_employees,email',
            'phone' => 'nullable|string|max:50',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'hr_position_id' => 'required|exists:hr_positions,id',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'annual_leave_days' => 'nullable|integer|min:0',
        ]);

        $code = 'EMP-' . strtoupper(substr(uniqid(), -5));
        $leaveDays = $validated['annual_leave_days'] ?? 15;

        HrEmployee::create([
            'employee_code' => $code,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'hr_department_id' => $validated['hr_department_id'],
            'hr_position_id' => $validated['hr_position_id'],
            'hire_date' => $validated['hire_date'],
            'salary_cents' => (int) round($validated['salary'] * 100),
            'annual_leave_days' => $leaveDays,
            'remaining_leave_days' => $leaveDays,
            'status' => 'active',
        ]);

        return redirect()->route('employees.index')->with('success', "Employee {$validated['first_name']} {$validated['last_name']} registered as {$code}.");
    }

    public function exportCsv(): StreamedResponse
    {
        $employees = HrEmployee::with(['department', 'position'])->get();

        $response = new StreamedResponse(function () use ($employees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee Code', 'Full Name', 'Email', 'Phone', 'Department', 'Position', 'Salary ($)', 'Status', 'Remaining Leave']);

            foreach ($employees as $e) {
                fputcsv($handle, [
                    $e->employee_code,
                    $e->full_name,
                    $e->email,
                    $e->phone ?? '',
                    $e->department->name ?? '',
                    $e->position->title ?? '',
                    number_format($e->salary_cents / 100, 2),
                    $e->status,
                    $e->remaining_leave_days,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="employee_directory_' . date('Y-m-d') . '.csv"');

        return $response;
    }
}
