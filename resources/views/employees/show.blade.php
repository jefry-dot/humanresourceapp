@extends('layouts.dashboard')

@section('title', 'Employee Detail')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employee Detail</h3>
                <p class="text-subtitle text-muted">View detailed employee information</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Employee Header Card -->
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl bg-primary me-3">
                                <span class="avatar-content">{{ strtoupper(substr($employee->fullname, 0, 2)) }}</span>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $employee->fullname }}</h4>
                                <p class="text-muted mb-0">{{ $employee->role->title ?? '-' }} • {{ $employee->department->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @if($employee->status == 'active')
                            <span class="badge bg-success fs-6">Active</span>
                        @elseif($employee->status == 'inactive')
                            <span class="badge bg-danger fs-6">Inactive</span>
                        @else
                            <span class="badge bg-warning fs-6">On Leave</span>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge"></i> Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Full Name</td>
                                <td>{{ $employee->fullname }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Email</td>
                                <td>
                                    <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Phone Number</td>
                                <td>
                                    @if($employee->phone_number)
                                        <a href="tel:{{ $employee->phone_number }}">{{ $employee->phone_number }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Address</td>
                                <td>{{ $employee->address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Employment Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-briefcase"></i> Employment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Department</td>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Role</td>
                                <td>{{ $employee->role->title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hire Date</td>
                                <td>{{ $employee->hire_date ? $employee->hire_date->format('d M Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status</td>
                                <td>
                                    @if($employee->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($employee->status == 'inactive')
                                        <span class="badge bg-danger">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">On Leave</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Salary Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cash-stack"></i> Salary Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Monthly Salary</td>
                                <td class="fs-5 text-success fw-bold">
                                    {{ $employee->salary ? 'Rp ' . number_format($employee->salary, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history"></i> System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Created At</td>
                                <td>{{ $employee->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Last Updated</td>
                                <td>{{ $employee->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
