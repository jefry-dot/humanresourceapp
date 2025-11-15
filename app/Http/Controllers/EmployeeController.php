<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'role'])
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::where('status', 'active')->get();
        $roles = Role::all();

        return view('employees.index', compact('employees', 'departments', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'role']);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('status', 'active')->get();
        $roles = Role::all();

        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }
}
