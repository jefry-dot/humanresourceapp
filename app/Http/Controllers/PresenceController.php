<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Employee;
use App\Models\CompanySetting;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin & HR: Show management view
        if (in_array($user->role, ['admin', 'hr'])) {
            $presences = Presence::with('employee')
                ->orderBy('date', 'desc')
                ->orderBy('check_in', 'desc')
                ->get();

            $employees = Employee::where('status', 'active')->get();

            return view('presences.admin', compact('presences', 'employees'));
        }
        // Manager & Employee: Show attendance view with GPS
        else {
            $employee = Employee::where('email', $user->email)->first();

            if (!$employee) {
                return redirect()->route('dashboard')->with('error', 'Employee record not found!');
            }

            // Get today's presence
            $todayPresence = Presence::where('employee_id', $employee->id)
                ->whereDate('date', today())
                ->first();

            // Get presence history
            $presences = Presence::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->orderBy('check_in', 'desc')
                ->limit(10)
                ->get();

            $settings = CompanySetting::getSettings();

            return view('presences.employee', compact('employee', 'todayPresence', 'presences', 'settings'));
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
        ]);

        // Combine date with time for datetime fields
        $validated['check_in'] = $validated['date'] . ' ' . $validated['check_in'];
        if (isset($validated['check_out'])) {
            $validated['check_out'] = $validated['date'] . ' ' . $validated['check_out'];
        }

        Presence::create($validated);

        return redirect()->route('presences.index')->with('success', 'Presence created successfully!');
    }

    public function edit(Presence $presence)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('presences.edit', compact('presence', 'employees'));
    }

    public function update(Request $request, Presence $presence)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
        ]);

        // Combine date with time for datetime fields
        $validated['check_in'] = $validated['date'] . ' ' . $validated['check_in'];
        if (isset($validated['check_out'])) {
            $validated['check_out'] = $validated['date'] . ' ' . $validated['check_out'];
        }

        $presence->update($validated);

        return redirect()->route('presences.index')->with('success', 'Presence updated successfully!');
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();

        return redirect()->route('presences.index')->with('success', 'Presence deleted successfully!');
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee record not found!'], 404);
        }

        // Check if already checked in today
        $existingPresence = Presence::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        if ($existingPresence) {
            return response()->json(['error' => 'You have already checked in today!'], 422);
        }

        // Get company settings
        $settings = CompanySetting::getSettings();

        // Validate location if office coordinates are set
        if ($settings->office_latitude && $settings->office_longitude) {
            $distance = $this->calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $settings->office_latitude,
                $settings->office_longitude
            );

            if ($distance > $settings->max_radius_meters) {
                return response()->json([
                    'error' => "You are too far from the office! Distance: " . round($distance) . " meters. Maximum allowed: {$settings->max_radius_meters} meters."
                ], 422);
            }
        }

        // Determine status based on check-in time
        $checkInTime = Carbon::now();
        $workStartTime = Carbon::parse($settings->work_start_time);
        $status = $checkInTime->lte($workStartTime) ? 'present' : 'late';

        // Create presence record
        $presence = Presence::create([
            'employee_id' => $employee->id,
            'date' => today(),
            'check_in' => $checkInTime,
            'status' => $status,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'presence' => $presence,
        ]);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee record not found!'], 404);
        }

        // Find today's presence
        $presence = Presence::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        if (!$presence) {
            return response()->json(['error' => 'You have not checked in today!'], 422);
        }

        if ($presence->check_out) {
            return response()->json(['error' => 'You have already checked out today!'], 422);
        }

        // Get company settings
        $settings = CompanySetting::getSettings();

        // Validate location if office coordinates are set
        if ($settings->office_latitude && $settings->office_longitude) {
            $distance = $this->calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $settings->office_latitude,
                $settings->office_longitude
            );

            if ($distance > $settings->max_radius_meters) {
                return response()->json([
                    'error' => "You are too far from the office! Distance: " . round($distance) . " meters. Maximum allowed: {$settings->max_radius_meters} meters."
                ], 422);
            }
        }

        // Update presence with check-out time
        $presence->update([
            'check_out' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out successful!',
            'presence' => $presence,
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
