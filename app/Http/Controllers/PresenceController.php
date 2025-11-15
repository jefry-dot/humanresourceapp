<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Employee;

class PresenceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin & HR: See all presences
        if (in_array($user->role, ['admin', 'hr'])) {
            $presences = Presence::with('employee')
                ->orderBy('date', 'desc')
                ->orderBy('check_in', 'desc')
                ->get();
        }
        // Manager: See team presences only (employees in their department)
        elseif ($user->role === 'manager') {
            // Find manager's employee record
            $manager = Employee::where('email', $user->email)->first();

            if ($manager) {
                // Get all employees in the same department
                $teamEmployeeIds = Employee::where('department_id', $manager->department_id)
                    ->pluck('id');

                $presences = Presence::with('employee')
                    ->whereIn('employee_id', $teamEmployeeIds)
                    ->orderBy('date', 'desc')
                    ->orderBy('check_in', 'desc')
                    ->get();
            } else {
                $presences = collect();
            }
        }
        // Employee: Only see own presences
        else {
            $employee = Employee::where('email', $user->email)->first();

            if ($employee) {
                $presences = Presence::with('employee')
                    ->where('employee_id', $employee->id)
                    ->orderBy('date', 'desc')
                    ->orderBy('check_in', 'desc')
                    ->get();
            } else {
                $presences = collect();
            }
        }

        $employees = Employee::where('status', 'active')->get();

        return view('presences.index', compact('presences', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
        ]);

        // Combine date with time for datetime fields
        $validated['check_in'] = $validated['date'] . ' ' . $validated['check_in'];
        if (isset($validated['check_out'])) {
            $validated['check_out'] = $validated['date'] . ' ' . $validated['check_out'];
        }

        Presence::create($validated);

        return redirect()->route('presences.index')->with('success', 'Presence created successfully!');
    }

    public function edit(Presence $presence)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('presences.edit', compact('presence', 'employees'));
    }

    public function update(Request $request, Presence $presence)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
        ]);

        // Combine date with time for datetime fields
        $validated['check_in'] = $validated['date'] . ' ' . $validated['check_in'];
        if (isset($validated['check_out'])) {
            $validated['check_out'] = $validated['date'] . ' ' . $validated['check_out'];
        }

        $presence->update($validated);

        return redirect()->route('presences.index')->with('success', 'Presence updated successfully!');
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();

        return redirect()->route('presences.index')->with('success', 'Presence deleted successfully!');
    }
}
