<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        $approver = User::first(); // Admin user as approver

        $leaveRequests = [
            [
                'employee_id' => $employees[0]->id,
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(14),
                'reason' => 'Family vacation to Bali',
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now()->subDays(2),
            ],
            [
                'employee_id' => $employees[1]->id,
                'leave_type' => 'sick',
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->subDays(1),
                'reason' => 'Flu and fever, need rest',
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now()->subDays(3),
            ],
            [
                'employee_id' => $employees[2]->id,
                'leave_type' => 'personal',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(7),
                'reason' => 'Personal matters to attend',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'employee_id' => $employees[3]->id,
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(25),
                'reason' => 'Wedding ceremony preparation',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'employee_id' => $employees[4]->id,
                'leave_type' => 'sick',
                'start_date' => Carbon::now()->addDays(1),
                'end_date' => Carbon::now()->addDays(2),
                'reason' => 'Medical checkup appointment',
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now()->subHours(5),
            ],
            [
                'employee_id' => $employees[5]->id,
                'leave_type' => 'maternity',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(120),
                'reason' => 'Maternity leave for childbirth',
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now()->subDays(1),
            ],
            [
                'employee_id' => $employees[6]->id,
                'leave_type' => 'unpaid',
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(20),
                'reason' => 'Continuing education program',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'employee_id' => $employees[7]->id,
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->subDays(3),
                'reason' => 'Year-end holiday break',
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($leaveRequests as $request) {
            LeaveRequest::create($request);
        }
    }
}
