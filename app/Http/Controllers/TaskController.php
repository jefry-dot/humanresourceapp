<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Employee;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('employee')->orderBy('created_at', 'desc')->get();
        $employees = Employee::where('status', 'active')->orderBy('fullname')->get();
        return view('tasks.index', compact('tasks', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        // Load employee relationship
        $task->load('employee');

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // Format the task data for the form
        $taskData = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'assigned_to' => $task->assigned_to, // This is employee_id
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
            'status' => $task->status,
        ];

        return response()->json($taskData);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        $statusMessages = [
            'pending' => 'Task marked as Pending!',
            'in_progress' => 'Task marked as In Progress!',
            'completed' => 'Task marked as Completed!',
        ];

        return redirect()->route('tasks.index')->with('success', $statusMessages[$validated['status']]);
    }
}
