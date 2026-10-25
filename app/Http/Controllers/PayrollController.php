<?php

namespace App\Http\Controllers;

use App\Models\HrEmployee;
use App\Models\HrPayroll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $payrolls = HrPayroll::where('period_month', $month)
            ->where('period_year', $year)
            ->with('employee.department')
            ->get();

        $employees = HrEmployee::where('status', 'active')->orderBy('first_name')->get();
        $totalPaidCents = (int) $payrolls->where('payment_status', 'paid')->sum('net_salary_cents');

        return view('payroll.index', compact('payrolls', 'employees', 'month', 'year', 'totalPaidCents'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hr_employee_id' => 'required|exists:hr_employees,id',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|between:2020,2050',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $employee = HrEmployee::findOrFail($validated['hr_employee_id']);
        $baseCents = $employee->salary_cents;
        $allowancesCents = (int) round(($validated['allowances'] ?? 0) * 100);
        $deductionsCents = (int) round(($validated['deductions'] ?? 0) * 100);
        $netCents = max(0, $baseCents + $allowancesCents - $deductionsCents);

        HrPayroll::updateOrCreate(
            [
                'hr_employee_id' => $employee->id,
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
            ],
            [
                'base_salary_cents' => $baseCents,
                'allowances_cents' => $allowancesCents,
                'deductions_cents' => $deductionsCents,
                'net_salary_cents' => $netCents,
                'payment_status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('payroll.index', [
            'month' => $validated['period_month'],
            'year' => $validated['period_year'],
        ])->with('success', "Payroll generated for {$employee->full_name}.");
    }

    public function markPaid(Request $request, HrPayroll $payroll): RedirectResponse
    {
        $payroll->update([
            'payment_status' => 'paid',
            'payment_date' => now(),
        ]);

        return back()->with('success', "Payroll for {$payroll->employee->full_name} marked as paid.");
    }
}
