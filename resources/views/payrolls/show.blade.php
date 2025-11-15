@extends('layouts.dashboard')

@section('title', 'Salary Slip')

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .page-heading .page-title,
        .breadcrumb {
            display: none !important;
        }
        body {
            background: white !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
    }
    .salary-slip {
        max-width: 800px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title no-print">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Salary Slip</h3>
                <p class="text-subtitle text-muted">Employee salary slip details</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payrolls</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Salary Slip</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="salary-slip">
            <div class="card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h2 class="mb-1">SALARY SLIP</h2>
                        <p class="text-muted mb-0">{{ $payroll->pay_date->format('F Y') }}</p>
                    </div>

                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Employee Name</td>
                                    <td>: {{ $payroll->employee->fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department</td>
                                    <td>: {{ $payroll->employee->department->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Position</td>
                                    <td>: {{ $payroll->employee->role->title ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Email</td>
                                    <td>: {{ $payroll->employee->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Pay Date</td>
                                    <td>: {{ $payroll->pay_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Pay Period</td>
                                    <td>: {{ $payroll->pay_date->format('F Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Earnings & Deductions -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-success">Earnings</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Amount (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Basic Salary</td>
                                        <td class="text-end">{{ number_format($payroll->salary, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($payroll->bonuses)
                                    <tr>
                                        <td>Bonuses</td>
                                        <td class="text-end text-success">{{ number_format($payroll->bonuses, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="fw-bold table-light">
                                        <td>Total Earnings</td>
                                        <td class="text-end">{{ number_format($payroll->salary + ($payroll->bonuses ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-danger">Deductions</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Amount (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($payroll->deductions)
                                    <tr>
                                        <td>Deductions</td>
                                        <td class="text-end text-danger">{{ number_format($payroll->deductions, 0, ',', '.') }}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No deductions</td>
                                    </tr>
                                    @endif
                                    <tr class="fw-bold table-light">
                                        <td>Total Deductions</td>
                                        <td class="text-end">{{ number_format($payroll->deductions ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Net Salary -->
                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-12">
                            <div class="bg-primary text-white p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">NET SALARY</h4>
                                    <h3 class="mb-0">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Note -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <p class="text-muted small mb-0"><strong>Note:</strong> This is a computer-generated salary slip. No signature is required.</p>
                            <p class="text-muted small mb-0">Generated on: {{ now()->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4 no-print">
                        <div class="col-12 d-flex justify-content-between">
                            <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Print Slip
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
