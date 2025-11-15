<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, and HR policies',
                'status' => 'active'
            ],
            [
                'name' => 'Information Technology',
                'description' => 'Handles IT infrastructure, software development, and technical support',
                'status' => 'active'
            ],
            [
                'name' => 'Finance',
                'description' => 'Manages company finances, accounting, and budgeting',
                'status' => 'active'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Handles marketing campaigns, branding, and customer outreach',
                'status' => 'active'
            ],
            [
                'name' => 'Sales',
                'description' => 'Manages sales operations and customer relationships',
                'status' => 'active'
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees day-to-day business operations',
                'status' => 'active'
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
