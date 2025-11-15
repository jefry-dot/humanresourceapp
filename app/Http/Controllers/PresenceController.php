<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Employee;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::with('employee')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->get();

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
