<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['title' => 'Manager', 'description' => 'Department Manager'],
            ['title' => 'Senior Developer', 'description' => 'Senior Software Developer'],
            ['title' => 'Developer', 'description' => 'Software Developer'],
            ['title' => 'HR Specialist', 'description' => 'Human Resources Specialist'],
            ['title' => 'Accountant', 'description' => 'Financial Accountant'],
            ['title' => 'Marketing Executive', 'description' => 'Marketing Executive'],
            ['title' => 'Sales Representative', 'description' => 'Sales Representative'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
