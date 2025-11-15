@extends('layouts.dashboard')

@section('title', 'Payrolls Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/table-datatable.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Payrolls Management</h3>
                <p class="text-subtitle text-muted">View employee payroll records</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payrolls</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    All Payrolls
                </h5>
                <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Payroll
                </a>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Pay Date</th>
                            <th>Salary</th>
                            <th>Bonuses</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        <tr>
                            <td>{{ $payroll->employee->fullname }}</td>
                            <td>{{ $payroll->pay_date->format('d M Y') }}</td>
                            <td>Rp {{ number_format($payroll->salary, 0, ',', '.') }}</td>
                            <td>
                                @if($payroll->bonuses)
                                    <span class="text-success">+Rp {{ number_format($payroll->bonuses, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($payroll->deductions)
                                    <span class="text-danger">-Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-bold text-primary">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deletePayroll({{ $payroll->id }}, '{{ $payroll->employee->fullname }} - {{ $payroll->pay_date->format('d M Y') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No payroll records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Delete Payroll Modal -->
<div class="modal fade" id="deletePayrollModal" tabindex="-1" aria-labelledby="deletePayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePayrollModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payroll record?</p>
                <p class="fw-bold" id="deletePayrollName"></p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePayrollForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('mazer/dist/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('mazer/dist/assets/static/js/pages/simple-datatables.js') }}"></script>

<script>
    // Function to delete payroll
    function deletePayroll(payrollId, payrollName) {
        // Set payroll name in modal
        document.getElementById('deletePayrollName').textContent = payrollName;

        // Set form action
        document.getElementById('deletePayrollForm').action = `/payrolls/${payrollId}`;

        // Show modal
        var deletePayrollModal = new bootstrap.Modal(document.getElementById('deletePayrollModal'));
        deletePayrollModal.show();
    }
</script>
@endpush
