<?php

namespace App\Http\Controllers;

use App\Models\HrEmployee;
use App\Models\HrLeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function index(): View
    {
        $requests = HrLeaveRequest::with(['employee', 'approver'])->latest()->paginate(15);
        $employees = HrEmployee::where('status', 'active')->orderBy('first_name')->get();

        return view('leaves.index', compact('requests', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hr_employee_id' => 'required|exists:hr_employees,id',
            'leave_type' => 'required|in:annual,sick,maternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_count' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $employee = HrEmployee::findOrFail($validated['hr_employee_id']);

        if ($validated['leave_type'] === 'annual' && $employee->remaining_leave_days < $validated['days_count']) {
            return back()->with('error', "Employee only has {$employee->remaining_leave_days} remaining leave days.");
        }

        $employee->leaveRequests()->create($validated);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted.');
    }

    public function process(Request $request, HrLeaveRequest $leaveRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'approver_notes' => 'nullable|string',
        ]);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Leave request has already been processed.');
        }

        DB::transaction(function () use ($leaveRequest, $validated, $request) {
            $employee = $leaveRequest->employee;

            if ($validated['status'] === 'approved' && $leaveRequest->leave_type === 'annual') {
                if ($employee->remaining_leave_days < $leaveRequest->days_count) {
                    abort(422, 'Insufficient leave days remaining.');
                }
                $employee->decrement('remaining_leave_days', $leaveRequest->days_count);
            }

            $leaveRequest->update([
                'status' => $validated['status'],
                'approved_by' => $request->user()->id,
                'approver_notes' => $validated['approver_notes'] ?? null,
            ]);
        });

        return redirect()->route('leaves.index')->with('success', "Leave request {$validated['status']}.");
    }
}
