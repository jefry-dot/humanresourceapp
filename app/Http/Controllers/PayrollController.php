<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')
            ->orderBy('pay_date', 'desc')
            ->get();

        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();

        return view('payrolls.create', compact('employees'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.role']);

        return view('payrolls.show', compact('payroll'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
        ]);

        // Calculate net salary
        $salary = $validated['salary'];
        $bonuses = $validated['bonuses'] ?? 0;
        $deductions = $validated['deductions'] ?? 0;
        $validated['net_salary'] = $salary + $bonuses - $deductions;

        Payroll::create($validated);

        return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully!');
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
        ]);

        // Calculate net salary
        $salary = $validated['salary'];
        $bonuses = $validated['bonuses'] ?? 0;
        $deductions = $validated['deductions'] ?? 0;
        $validated['net_salary'] = $salary + $bonuses - $deductions;

        $payroll->update($validated);

        return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully!');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully!');
    }
}
