<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\Department;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get statistics based on role
        $stats = [];
        $latestTasks = [];

        if (in_array($user->role, ['admin', 'hr'])) {
            // Admin & HR: Full statistics
            $stats = [
                'total_employees' => Employee::where('status', 'active')->count(),
                'total_departments' => Department::count(),
                'total_tasks' => Task::count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'today_presences' => Presence::whereDate('date', today())->count(),
                'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
                'approved_leaves_this_month' => LeaveRequest::where('status', 'approved')
                    ->whereMonth('start_date', Carbon::now()->month)
                    ->count(),
                'employees_on_leave_today' => LeaveRequest::where('status', 'approved')
                    ->whereDate('start_date', '<=', today())
                    ->whereDate('end_date', '>=', today())
                    ->count(),
            ];

            // Get latest tasks for admin/hr
            $latestTasks = Task::orderBy('created_at', 'desc')->take(5)->get();
        } elseif ($user->role === 'manager') {
            // Manager: Team statistics
            $manager = Employee::where('email', $user->email)->first();

            if ($manager) {
                $teamEmployeeIds = Employee::where('department_id', $manager->department_id)->pluck('id');

                $stats = [
                    'team_members' => $teamEmployeeIds->count(),
                    'total_tasks' => Task::count(),
                    'pending_tasks' => Task::where('status', 'pending')->count(),
                    'today_presences' => Presence::whereIn('employee_id', $teamEmployeeIds)
                        ->whereDate('date', today())
                        ->count(),
                    'team_on_leave_today' => LeaveRequest::whereIn('employee_id', $teamEmployeeIds)
                        ->where('status', 'approved')
                        ->whereDate('start_date', '<=', today())
                        ->whereDate('end_date', '>=', today())
                        ->count(),
                ];

                // Get latest tasks for manager
                $latestTasks = Task::orderBy('created_at', 'desc')->take(5)->get();
            }
        } else {
            // Employee: Personal statistics
            $employee = Employee::where('email', $user->email)->first();

            if ($employee) {
                $stats = [
                    'my_tasks' => Task::count(),
                    'pending_tasks' => Task::where('status', 'pending')->count(),
                    'my_presences_this_month' => Presence::where('employee_id', $employee->id)
                        ->whereMonth('date', Carbon::now()->month)
                        ->count(),
                    'my_leave_requests' => LeaveRequest::where('employee_id', $employee->id)->count(),
                    'pending_leave_requests' => LeaveRequest::where('employee_id', $employee->id)
                        ->where('status', 'pending')
                        ->count(),
                ];

                // Get latest tasks for employee (assigned to them)
                $latestTasks = Task::where('assigned_to', $employee->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }
        }

        return view('dashboard.index', compact('stats', 'latestTasks'));
    }
}
