@extends('layouts.dashboard')

@section('title', 'Task Details')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Task Details</h3>
                <p class="text-subtitle text-muted">View detailed information about this task</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Task Information Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Task Information</h5>
                        <div>
                            @if($task->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($task->status == 'in_progress')
                                <span class="badge bg-warning">In Progress</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Title</label>
                            <h4>{{ $task->title }}</h4>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Description</label>
                            <p style="white-space: pre-wrap;">{{ $task->description ?? '-' }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Assigned To</label>
                                <p class="mb-0">
                                    @if($task->employee)
                                        <i class="bi bi-person-fill"></i> {{ $task->employee->fullname }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Due Date</label>
                                <p class="mb-0">
                                    @if($task->due_date)
                                        <i class="bi bi-calendar-event"></i>
                                        <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-bold' : '' }}">
                                            {{ $task->due_date->format('d M Y') }}
                                        </span>
                                        @if($task->due_date->isPast() && $task->status !== 'completed')
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No due date</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Created At</label>
                                <p class="mb-0 small text-muted">
                                    <i class="bi bi-clock-history"></i> {{ $task->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Last Updated</label>
                                <p class="mb-0 small text-muted">
                                    <i class="bi bi-clock-history"></i> {{ $task->updated_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <!-- Update Status -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <div class="d-grid gap-2">
                                @if($task->status != 'pending')
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="bi bi-clock"></i> Mark as Pending
                                    </button>
                                </form>
                                @endif

                                @if($task->status != 'in_progress')
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-arrow-repeat"></i> Mark as In Progress
                                    </button>
                                </form>
                                @endif

                                @if($task->status != 'completed')
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle"></i> Mark as Completed
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- Edit & Delete Actions (Only for Admin, HR, Manager) -->
                        @if(in_array(auth()->user()->role, ['admin', 'hr', 'manager']))
                        <div class="mb-3">
                            <label class="form-label fw-bold">Manage Task</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary w-100" onclick="window.location.href='{{ route('tasks.index') }}#edit-{{ $task->id }}'">
                                    <i class="bi bi-pencil"></i> Edit Task
                                </button>
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                                    <i class="bi bi-trash"></i> Delete Task
                                </button>
                            </div>
                        </div>
                        <hr>
                        @endif

                        <!-- Back Button -->
                        <div class="d-grid">
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Tasks
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Task Info Summary -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-speedometer2"></i> Task Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Task ID</small>
                            <p class="mb-0 fw-bold">#{{ $task->id }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Current Status</small>
                            <p class="mb-0">
                                @if($task->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($task->status == 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </p>
                        </div>
                        @if($task->due_date)
                        <div class="mb-0">
                            <small class="text-muted">Days Until Due</small>
                            <p class="mb-0">
                                @php
                                    $daysUntilDue = now()->diffInDays($task->due_date, false);
                                @endphp
                                @if($task->status == 'completed')
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Completed</span>
                                @elseif($daysUntilDue < 0)
                                    <span class="text-danger fw-bold">{{ abs($daysUntilDue) }} days overdue</span>
                                @elseif($daysUntilDue == 0)
                                    <span class="text-warning fw-bold">Due today!</span>
                                @else
                                    <span class="fw-bold">{{ $daysUntilDue }} days remaining</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this task?</p>
                    <p class="fw-bold">{{ $task->title }}</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
