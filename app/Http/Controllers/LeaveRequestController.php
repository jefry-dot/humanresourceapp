<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with(['employee', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();

        return view('leave_requests.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:annual,sick,personal,maternity,paternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        LeaveRequest::create($validated);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request created successfully!');
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
}
