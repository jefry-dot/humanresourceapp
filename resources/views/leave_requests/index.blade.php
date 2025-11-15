@extends('layouts.dashboard')

@section('title', 'Leave Requests Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/table-datatable.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Leave Requests Management</h3>
                <p class="text-subtitle text-muted">Manage employee leave requests</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Leave Requests</li>
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

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    All Leave Requests
                </h5>
                {{-- Only Admin, HR, and Employee can create leave requests --}}
                @if(in_array(auth()->user()->role, ['admin', 'hr', 'employee']))
                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Leave Request
                </a>
                @endif
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $request)
                        <tr>
                            <td>{{ $request->employee->fullname }}</td>
                            <td>
                                @if($request->leave_type == 'annual')
                                    <span class="badge bg-primary">Annual</span>
                                @elseif($request->leave_type == 'sick')
                                    <span class="badge bg-warning">Sick</span>
                                @elseif($request->leave_type == 'personal')
                                    <span class="badge bg-info">Personal</span>
                                @elseif($request->leave_type == 'maternity')
                                    <span class="badge bg-success">Maternity</span>
                                @elseif($request->leave_type == 'paternity')
                                    <span class="badge bg-success">Paternity</span>
                                @else
                                    <span class="badge bg-secondary">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ $request->start_date->format('d M Y') }}</td>
                            <td>{{ $request->end_date->format('d M Y') }}</td>
                            <td>{{ $request->days }} day(s)</td>
                            <td>{{ Str::limit($request->reason, 50) }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                {{-- Only Admin and HR can Edit/Delete leave requests --}}
                                @if(in_array(auth()->user()->role, ['admin', 'hr']))
                                <div class="btn-group" role="group">
                                    <a href="{{ route('leave-requests.edit', $request->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deleteLeaveRequest({{ $request->id }}, '{{ $request->employee->fullname }} - {{ $request->leave_type }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @else
                                <span class="badge bg-secondary">View Only</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No leave requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Delete Leave Request Modal -->
<div class="modal fade" id="deleteLeaveRequestModal" tabindex="-1" aria-labelledby="deleteLeaveRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteLeaveRequestModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this leave request?</p>
                <p class="fw-bold" id="deleteLeaveRequestName"></p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteLeaveRequestForm" method="POST" style="display: inline;">
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
    // Function to delete leave request
    function deleteLeaveRequest(requestId, requestName) {
        // Set request name in modal
        document.getElementById('deleteLeaveRequestName').textContent = requestName;

        // Set form action
        document.getElementById('deleteLeaveRequestForm').action = `/leave-requests/${requestId}`;

        // Show modal
        var deleteLeaveRequestModal = new bootstrap.Modal(document.getElementById('deleteLeaveRequestModal'));
        deleteLeaveRequestModal.show();
    }
</script>
@endpush
