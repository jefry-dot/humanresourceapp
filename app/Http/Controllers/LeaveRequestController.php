<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;


class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin & HR: See all leave requests
        if (in_array($user->role, ['admin', 'hr'])) {
            $leaveRequests = LeaveRequest::with(['employee', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        // Employee: Only see own leave requests
        else {
            // Find employee record by user email or employee_id
            $employee = Employee::where('email', $user->email)->first();

            if ($employee) {
                $leaveRequests = LeaveRequest::with(['employee', 'approver'])
                    ->where('employee_id', $employee->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $leaveRequests = collect(); // Empty collection if no employee found
            }
        }

        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $user = auth()->user();
        $employees = Employee::where('status', 'active')->get();

        // Get current employee if not admin/hr
        $currentEmployee = null;
        if (!in_array($user->role, ['admin', 'hr'])) {
            $currentEmployee = Employee::where('email', $user->email)->first();
        }

        return view('leave_requests.create', compact('employees', 'currentEmployee'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // If employee, auto-fill employee_id
        if (!in_array($user->role, ['admin', 'hr'])) {
            $employee = Employee::where('email', $user->email)->first();
            if ($employee) {
                $request->merge(['employee_id' => $employee->id]);
            }
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:annual,sick,personal,maternity,paternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        LeaveRequest::create($validated);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully!');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('leave_requests.edit', compact('leaveRequest', 'employees'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:annual,sick,personal,maternity,paternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // If status is being changed to approved, set approved_by and approved_at
        if ($validated['status'] === 'approved' && $leaveRequest->status !== 'approved') {
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
        }

        // If status is changed from approved to pending/rejected, clear approval data
        if ($validated['status'] !== 'approved' && $leaveRequest->status === 'approved') {
            $validated['approved_by'] = null;
            $validated['approved_at'] = null;
        }

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully!');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully!');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        // Only allow if still pending
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be approved!');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request approved successfully!');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        // Only allow if still pending
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be rejected!');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request rejected!');
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->first();

        // Check if this is employee's own request
        if (!$employee || $leaveRequest->employee_id !== $employee->id) {
            return redirect()->route('leave-requests.index')->with('error', 'You can only cancel your own leave requests!');
        }

        // Only allow cancel if still pending
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be cancelled!');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request cancelled successfully!');
    }
}
