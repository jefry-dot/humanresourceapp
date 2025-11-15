@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <h3>Dashboard - {{ ucfirst(auth()->user()->role) }}</h3>
    <p class="text-muted">Welcome back, {{ \App\Models\Employee::where('email', auth()->user()->email)->first()->fullname ?? auth()->user()->name }}!</p>
</div>

<section class="row">
    <div class="col-12">
        <div class="row">
            @if(in_array(auth()->user()->role, ['admin', 'hr']))
                {{-- Admin & HR Dashboard --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Employees</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['total_employees'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Today's Attendance</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['today_presences'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Departments</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['total_departments'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Tasks</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['total_tasks'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon orange mb-2" style="background-color: #ff9800;">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pending Tasks</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['pending_tasks'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon" style="background-color: #ffc107;">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pending Leave Requests</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['pending_leaves'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon" style="background-color: #28a745;">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Approved Leaves (This Month)</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['approved_leaves_this_month'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon" style="background-color: #dc3545;">
                                        <i class="bi bi-person-x-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Employees On Leave Today</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['employees_on_leave_today'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role === 'manager')
                {{-- Manager Dashboard --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Team Members</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['team_members'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Team Present Today</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['today_presences'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Tasks</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['total_tasks'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-person-x-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Team On Leave Today</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['team_on_leave_today'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- Employee Dashboard --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">My Tasks</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['my_tasks'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon orange mb-2" style="background-color: #ff9800;">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pending Tasks</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['pending_tasks'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">My Attendance (This Month)</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['my_presences_this_month'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-calendar-x-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">My Leave Requests</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['my_leave_requests'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon" style="background-color: #ffc107;">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pending Leave Requests</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['pending_leave_requests'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Latest Tasks Section --}}
        @if(count($latestTasks) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="bi bi-list-task"></i> Latest Tasks</h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary">
                            View All <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestTasks as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ $task->title }}</strong>
                                        </td>
                                        <td>
                                            {{ Str::limit($task->description ?? '-', 50) }}
                                        </td>
                                        <td>
                                            @if($task->due_date)
                                                <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
                                                    {{ $task->due_date->format('d M Y') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($task->status == 'in_progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @elseif($task->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Quick Actions Section --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(in_array(auth()->user()->role, ['admin', 'hr', 'manager', 'employee']))
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('presences.index') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-check-fill"></i>
                                    @if(in_array(auth()->user()->role, ['admin', 'hr']))
                                        Manage Attendance
                                    @else
                                        My Attendance
                                    @endif
                                </a>
                            </div>
                            @endif

                            @if(in_array(auth()->user()->role, ['admin', 'hr', 'employee']))
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('leave-requests.index') }}" class="btn btn-success w-100">
                                    <i class="bi bi-calendar-x-fill"></i> Leave Requests
                                </a>
                            </div>
                            @endif

                            @if(in_array(auth()->user()->role, ['admin', 'hr', 'manager', 'employee']))
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('tasks.index') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-clipboard-check"></i> Tasks
                                </a>
                            </div>
                            @endif

                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('employee-profile.index') }}" class="btn btn-info w-100">
                                    <i class="bi bi-person-circle"></i> My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
