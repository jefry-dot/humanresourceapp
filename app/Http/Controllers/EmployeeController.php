<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'role'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employees.index', compact('employees'));
    }
}
