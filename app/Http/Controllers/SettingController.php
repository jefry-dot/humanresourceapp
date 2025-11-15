<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
            'max_radius_meters' => 'required|integer|min:10|max:10000',
        ]);

        $settings = CompanySetting::getSettings();
        $settings->update($validated);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }
}
