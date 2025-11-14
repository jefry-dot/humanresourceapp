<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class HumanResourcesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Insert Departments
        $departmentIds = [];
        foreach ([
            ['HR', 'Human Resources'],
            ['IT', 'Information Technology'],
            ['Sales', 'Departemen Sales'],
        ] as $dept) {
            $departmentIds[] = DB::table('departments')->insertGetId([
                'name' => $dept[0],
                'description' => $dept[1],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert Roles
        $roleIds = [];
        foreach ([
            ['HR', 'Handling team'],
            ['Developer', 'Handling codes'],
            ['Sales', 'Handling selling'],
        ] as $role) {
            $roleIds[] = DB::table('roles')->insertGetId([
                'title' => $role[0],
                'description' => $role[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert Employees (2 orang supaya presences & leave_requests valid)
        $employeeIds = [];
        for ($i = 0; $i < 2; $i++) {
            $employeeIds[] = DB::table('employees')->insertGetId([
                'fullname' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'hire_date' => Carbon::now(),
                'department_id' => $faker->randomElement($departmentIds),
                'role_id' => $faker->randomElement($roleIds),
                'status' => 'active',
                'salary' => $faker->randomFloat(2, 3000, 6000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert Tasks
        DB::table('tasks')->insert([
            [
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'assigned_to' => $employeeIds[0],
                'due_date' => Carbon::parse('2025-02-15'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'assigned_to' => $employeeIds[0],
                'due_date' => Carbon::parse('2025-02-15'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert Payroll
        $salary = $faker->randomFloat(2, 3000, 6000);
        $bonuses = $faker->randomFloat(2, 500, 1000);
        $deductions = $faker->randomFloat(2, 200, 500);
        $netSalary = $salary + $bonuses - $deductions;

        DB::table('payroll')->insert([
            'employee_id' => $employeeIds[0],
            'salary' => $salary,
            'bonuses' => $bonuses,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'pay_date' => Carbon::parse('2025-02-15'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Presences
        DB::table('presences')->insert([
            [
                'employee_id' => $employeeIds[0],
                'check_in' => Carbon::parse('2025-02-10 09:00:00'),
                'check_out' => Carbon::parse('2025-02-10 17:00:00'),
                'date' => Carbon::parse('2025-02-10'),
                'status' => 'present',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => $employeeIds[1],
                'check_in' => Carbon::parse('2025-02-10 09:00:00'),
                'check_out' => Carbon::parse('2025-02-10 17:00:00'),
                'date' => Carbon::parse('2025-02-10'),
                'status' => 'present',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert Leave Requests
        DB::table('leave_requests')->insert([
            [
                'employee_id' => $employeeIds[0],
                'leave_type' => 'Sick Leave',
                'start_date' => Carbon::parse('2025-02-20'),
                'end_date' => Carbon::parse('2025-02-23'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => $employeeIds[1],
                'leave_type' => 'Vacation',
                'start_date' => Carbon::parse('2025-02-20'),
                'end_date' => Carbon::parse('2025-02-23'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
