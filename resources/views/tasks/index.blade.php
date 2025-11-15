@extends('layouts.dashboard')

@section('title', 'Tasks Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/table-datatable.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tasks Management</h3>
                <p class="text-subtitle text-muted">Manage and track all your tasks</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tasks</li>
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
                    All Tasks
                </h5>
                {{-- Only Admin, HR, and Manager can create tasks --}}
                @if(in_array(auth()->user()->role, ['admin', 'hr', 'manager']))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-circle"></i> New Task
                </button>
                @endif
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->employee ? $task->employee->fullname : '-' }}</td>
                            <td>{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</td>
                            <td>
                                @if($task->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($task->status == 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($task->status != 'completed')
                                    <button type="button" class="btn btn-sm btn-success" title="Mark as Done" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    @endif
                                    @if($task->status != 'in_progress')
                                    <button type="button" class="btn btn-sm btn-warning" title="Mark as In Progress" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    @endif
                                    @if($task->status != 'pending')
                                    <button type="button" class="btn btn-sm btn-secondary" title="Mark as Pending" onclick="updateTaskStatus({{ $task->id }}, 'pending')">
                                        <i class="bi bi-clock"></i>
                                    </button>
                                    @endif
                                    {{-- Only Admin, HR, and Manager can Edit/Delete tasks --}}
                                    @if(in_array(auth()->user()->role, ['admin', 'hr', 'manager']))
                                    <button type="button" class="btn btn-sm btn-primary" title="Edit" onclick="editTask({{ $task->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deleteTask({{ $task->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No tasks found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->fullname }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select" id="edit_assigned_to" name="assigned_to">
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="edit_due_date" name="due_date">
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteTaskForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this task?</p>
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

<!-- Hidden form for status updates -->
<form id="statusUpdateForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="status_value">
</form>
@endsection

@push('scripts')
<script src="{{ asset('mazer/dist/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('mazer/dist/assets/static/js/pages/simple-datatables.js') }}"></script>

<script>
    // Reopen modal if there are validation errors
    @if($errors->any())
        var createTaskModal = new bootstrap.Modal(document.getElementById('createTaskModal'));
        createTaskModal.show();
    @endif

    // Function to edit task
    function editTask(taskId) {
        // Fetch task data
        fetch(`/tasks/${taskId}/edit`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(task => {
            // Populate form fields
            document.getElementById('edit_title').value = task.title || '';
            document.getElementById('edit_description').value = task.description || '';
            document.getElementById('edit_assigned_to').value = task.assigned_to || '';

            // Format due_date for input[type="date"] (YYYY-MM-DD)
            if (task.due_date) {
                // If due_date is already in YYYY-MM-DD format, use it directly
                // Otherwise, convert it
                let dateValue = task.due_date.split('T')[0]; // Handle ISO format
                document.getElementById('edit_due_date').value = dateValue;
            } else {
                document.getElementById('edit_due_date').value = '';
            }

            document.getElementById('edit_status').value = task.status || 'pending';

            // Update form action
            document.getElementById('editTaskForm').action = `/tasks/${taskId}`;

            // Show modal
            var editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
            editTaskModal.show();
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('Failed to load task data');
        });
    }

    // Function to delete task
    function deleteTask(taskId) {
        // Update form action
        document.getElementById('deleteTaskForm').action = `/tasks/${taskId}`;

        // Show modal
        var deleteTaskModal = new bootstrap.Modal(document.getElementById('deleteTaskModal'));
        deleteTaskModal.show();
    }

    // Function to update task status
    function updateTaskStatus(taskId, status) {
        // Set form action and status value
        document.getElementById('statusUpdateForm').action = `/tasks/${taskId}/status`;
        document.getElementById('status_value').value = status;

        // Submit the form
        document.getElementById('statusUpdateForm').submit();
    }
</script>
@endpush
