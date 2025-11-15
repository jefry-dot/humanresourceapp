<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Departments first if not exists
        $departments = [
            ['name' => 'Human Resources', 'description' => 'HR Department', 'status' => 'active'],
            ['name' => 'IT Department', 'description' => 'Information Technology', 'status' => 'active'],
            ['name' => 'Finance', 'description' => 'Finance Department', 'status' => 'active'],
            ['name' => 'Marketing', 'description' => 'Marketing Department', 'status' => 'active'],
            ['name' => 'Operations', 'description' => 'Operations Department', 'status' => 'active'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }

        // Create Roles first if not exists
        $roles = [
            ['title' => 'Manager', 'description' => 'Department Manager'],
            ['title' => 'Senior Staff', 'description' => 'Senior Level Staff'],
            ['title' => 'Staff', 'description' => 'Regular Staff'],
            ['title' => 'Intern', 'description' => 'Internship Position'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['title' => $role['title']], $role);
        }

        // Get IDs for reference
        $hrDept = Department::where('name', 'Human Resources')->first()->id;
        $itDept = Department::where('name', 'IT Department')->first()->id;
        $financeDept = Department::where('name', 'Finance')->first()->id;
        $marketingDept = Department::where('name', 'Marketing')->first()->id;
        $operationsDept = Department::where('name', 'Operations')->first()->id;

        $managerRole = Role::where('title', 'Manager')->first()->id;
        $seniorRole = Role::where('title', 'Senior Staff')->first()->id;
        $staffRole = Role::where('title', 'Staff')->first()->id;
        $internRole = Role::where('title', 'Intern')->first()->id;

        // Create Employees
        $employees = [
            [
                'fullname' => 'John Doe',
                'email' => 'john.doe@company.com',
                'phone_number' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'hire_date' => '2020-01-15',
                'department_id' => $itDept,
                'role_id' => $managerRole,
                'status' => 'active',
                'salary' => 15000000,
            ],
            [
                'fullname' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'phone_number' => '081234567891',
                'address' => 'Jl. Thamrin No. 45, Jakarta',
                'hire_date' => '2019-03-20',
                'department_id' => $hrDept,
                'role_id' => $managerRole,
                'status' => 'active',
                'salary' => 14000000,
            ],
            [
                'fullname' => 'Michael Johnson',
                'email' => 'michael.j@company.com',
                'phone_number' => '081234567892',
                'address' => 'Jl. Gatot Subroto No. 78, Jakarta',
                'hire_date' => '2021-06-10',
                'department_id' => $financeDept,
                'role_id' => $seniorRole,
                'status' => 'active',
                'salary' => 12000000,
            ],
            [
                'fullname' => 'Sarah Williams',
                'email' => 'sarah.w@company.com',
                'phone_number' => '081234567893',
                'address' => 'Jl. Kuningan No. 90, Jakarta',
                'hire_date' => '2022-02-14',
                'department_id' => $marketingDept,
                'role_id' => $seniorRole,
                'status' => 'active',
                'salary' => 11000000,
            ],
            [
                'fullname' => 'David Brown',
                'email' => 'david.b@company.com',
                'phone_number' => '081234567894',
                'address' => 'Jl. Rasuna Said No. 12, Jakarta',
                'hire_date' => '2022-08-01',
                'department_id' => $itDept,
                'role_id' => $staffRole,
                'status' => 'active',
                'salary' => 8000000,
            ],
            [
                'fullname' => 'Emily Davis',
                'email' => 'emily.d@company.com',
                'phone_number' => '081234567895',
                'address' => 'Jl. Senopati No. 34, Jakarta',
                'hire_date' => '2023-01-10',
                'department_id' => $operationsDept,
                'role_id' => $staffRole,
                'status' => 'active',
                'salary' => 7500000,
            ],
            [
                'fullname' => 'Robert Taylor',
                'email' => 'robert.t@company.com',
                'phone_number' => '081234567896',
                'address' => 'Jl. Kemang No. 56, Jakarta',
                'hire_date' => '2023-05-15',
                'department_id' => $financeDept,
                'role_id' => $staffRole,
                'status' => 'on_leave',
                'salary' => 7000000,
            ],
            [
                'fullname' => 'Jessica Martinez',
                'email' => 'jessica.m@company.com',
                'phone_number' => '081234567897',
                'address' => 'Jl. Menteng No. 67, Jakarta',
                'hire_date' => '2023-09-01',
                'department_id' => $marketingDept,
                'role_id' => $staffRole,
                'status' => 'active',
                'salary' => 6500000,
            ],
            [
                'fullname' => 'Christopher Lee',
                'email' => 'chris.lee@company.com',
                'phone_number' => '081234567898',
                'address' => 'Jl. Tebet No. 89, Jakarta',
                'hire_date' => '2024-03-01',
                'department_id' => $itDept,
                'role_id' => $internRole,
                'status' => 'active',
                'salary' => 4000000,
            ],
            [
                'fullname' => 'Amanda Anderson',
                'email' => 'amanda.a@company.com',
                'phone_number' => '081234567899',
                'address' => 'Jl. Blok M No. 23, Jakarta',
                'hire_date' => '2024-06-15',
                'department_id' => $hrDept,
                'role_id' => $internRole,
                'status' => 'active',
                'salary' => 3500000,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
