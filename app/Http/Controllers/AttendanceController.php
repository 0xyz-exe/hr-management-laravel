<?php

namespace App\Http\Controllers;

use App\Models\HrAttendance;
use App\Models\HrEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());
        $attendances = HrAttendance::where('date', $date)->with('employee')->get();
        $employees = HrEmployee::where('status', 'active')->orderBy('first_name')->get();

        return view('attendance.index', compact('attendances', 'employees', 'date'));
    }

    public function record(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hr_employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,late,absent,half_day',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        HrAttendance::updateOrCreate(
            [
                'hr_employee_id' => $validated['hr_employee_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('attendance.index', ['date' => $validated['date']])
            ->with('success', 'Attendance record saved.');
    }
}
