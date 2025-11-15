<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order (respecting foreign key dependencies)
        $this->call([
            UserSeeder::class,
            DepartmentSeeder::class,
            RoleSeeder::class,
            EmployeeSeeder::class,
            LeaveRequestSeeder::class,
        ]);
    }
}
