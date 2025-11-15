<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee record not found!');
        }

        return view('employee_profile.index', compact('employee', 'user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee record not found!');
        }

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update employee record
        $employee->update([
            'fullname' => $validated['fullname'],
            'phone_number' => $validated['phone_number'] ?? $employee->phone_number,
            'address' => $validated['address'] ?? $employee->address,
        ]);

        // Sync name to users table
        $user->update([
            'name' => $validated['fullname'],
        ]);

        // Update password if provided
        if ($request->filled('new_password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('employee-profile.index')->with('success', 'Profile and password updated successfully!');
        }

        return redirect()->route('employee-profile.index')->with('success', 'Profile updated successfully!');
    }
}
