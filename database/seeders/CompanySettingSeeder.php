<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanySetting;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanySetting::create([
            'work_start_time' => '09:00:00',
            'work_end_time' => '17:00:00',
            'office_latitude' => -6.200000, // Jakarta coordinates example
            'office_longitude' => 106.816666,
            'max_radius_meters' => 50,
        ]);
    }
}
