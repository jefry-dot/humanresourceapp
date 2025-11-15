<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'work_start_time',
        'work_end_time',
        'office_latitude',
        'office_longitude',
        'max_radius_meters',
    ];

    /**
     * Get the singleton instance of company settings
     */
    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'work_start_time' => '09:00:00',
            'work_end_time' => '17:00:00',
            'max_radius_meters' => 50,
        ]);
    }
}
