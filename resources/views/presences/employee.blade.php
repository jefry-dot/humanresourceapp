@extends('layouts.dashboard')

@section('title', 'My Attendance')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>My Attendance</h3>
                <p class="text-subtitle text-muted">Check-in and check-out for daily attendance</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div id="alertContainer"></div>

        <!-- Check-in/Check-out Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Today's Attendance - {{ now()->format('d F Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Current Time -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Current Time</h6>
                                <h3 id="currentTime" class="mb-0">--:--:--</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Check In Time -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="mb-2">Check In</h6>
                                <h3 class="mb-0">
                                    {{ $todayPresence && $todayPresence->check_in ? $todayPresence->check_in->format('H:i:s') : '--:--:--' }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Check Out Time -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6 class="mb-2">Check Out</h6>
                                <h3 class="mb-0">
                                    {{ $todayPresence && $todayPresence->check_out ? $todayPresence->check_out->format('H:i:s') : '--:--:--' }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Your Location (GPS)</label>
                        <div class="input-group">
                            <input type="text" id="locationDisplay" class="form-control" readonly placeholder="Waiting for GPS...">
                            <button class="btn btn-outline-secondary" type="button" id="refreshLocation">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        <small class="text-muted">Latitude: <span id="latDisplay">-</span> | Longitude: <span id="lonDisplay">-</span></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Office Location</label>
                        <div class="form-control-plaintext">
                            @if($settings->office_latitude && $settings->office_longitude)
                                Lat: {{ $settings->office_latitude }}, Lon: {{ $settings->office_longitude }}
                                <br><small class="text-muted">Max Distance: {{ $settings->max_radius_meters }} meters</small>
                            @else
                                <span class="text-warning">Not set by admin</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12 text-center">
                        @if(!$todayPresence)
                            <button type="button" id="checkInBtn" class="btn btn-success btn-lg" disabled>
                                <i class="bi bi-box-arrow-in-right"></i> Check In
                            </button>
                        @elseif(!$todayPresence->check_out)
                            <button type="button" id="checkOutBtn" class="btn btn-danger btn-lg" disabled>
                                <i class="bi bi-box-arrow-left"></i> Check Out
                            </button>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> You have completed attendance for today!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Attendance History (Last 10 Records)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presences as $presence)
                            <tr>
                                <td>{{ $presence->date->format('d M Y') }}</td>
                                <td>{{ $presence->check_in->format('H:i') }}</td>
                                <td>{{ $presence->check_out ? $presence->check_out->format('H:i') : '-' }}</td>
                                <td>
                                    @if($presence->status == 'present')
                                        <span class="badge bg-success">Present</span>
                                    @elseif($presence->status == 'late')
                                        <span class="badge bg-warning">Late</span>
                                    @else
                                        <span class="badge bg-danger">Absent</span>
                                    @endif
                                </td>
                                <td>
                                    @if($presence->latitude && $presence->longitude)
                                        <a href="https://www.google.com/maps?q={{ $presence->latitude }},{{ $presence->longitude }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="bi bi-geo-alt"></i> View
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No attendance records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    let userLatitude = null;
    let userLongitude = null;

    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour12: false });
        document.getElementById('currentTime').textContent = timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();

    // Get user location
    function getUserLocation() {
        if (navigator.geolocation) {
            // Request high accuracy GPS
            const options = {
                enableHighAccuracy: true,  // Use GPS instead of WiFi/IP
                timeout: 10000,             // Wait max 10 seconds
                maximumAge: 0               // Don't use cached location
            };

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLatitude = position.coords.latitude;
                    userLongitude = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    document.getElementById('latDisplay').textContent = userLatitude.toFixed(6);
                    document.getElementById('lonDisplay').textContent = userLongitude.toFixed(6);
                    document.getElementById('locationDisplay').value = `${userLatitude.toFixed(6)}, ${userLongitude.toFixed(6)} (±${Math.round(accuracy)}m)`;

                    // Show accuracy warning if too low
                    if (accuracy > 100) {
                        showAlert('warning', `Location accuracy is low (±${Math.round(accuracy)}m). For better accuracy, enable GPS on your device or go outdoors.`);
                    }

                    // Enable buttons
                    const checkInBtn = document.getElementById('checkInBtn');
                    const checkOutBtn = document.getElementById('checkOutBtn');
                    if (checkInBtn) checkInBtn.disabled = false;
                    if (checkOutBtn) checkOutBtn.disabled = false;
                },
                function(error) {
                    showAlert('danger', 'Error getting location: ' + error.message + '. Please enable location access.');
                },
                options  // Pass options for high accuracy
            );
        } else {
            showAlert('danger', 'Geolocation is not supported by your browser!');
        }
    }

    // Show alert
    function showAlert(type, message) {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.getElementById('alertContainer').innerHTML = alertHTML;

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alertElement = document.querySelector('#alertContainer .alert');
            if (alertElement) {
                alertElement.remove();
            }
        }, 5000);
    }

    // Check In
    const checkInBtn = document.getElementById('checkInBtn');
    if (checkInBtn) {
        checkInBtn.addEventListener('click', function() {
            if (!userLatitude || !userLongitude) {
                showAlert('warning', 'Please wait for GPS location...');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';

            fetch('{{ route('presences.checkin') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: userLatitude,
                    longitude: userLongitude
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', data.error || 'Check-in failed!');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Check In';
                }
            })
            .catch(error => {
                showAlert('danger', 'Error: ' + error.message);
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Check In';
            });
        });
    }

    // Check Out
    const checkOutBtn = document.getElementById('checkOutBtn');
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', function() {
            if (!userLatitude || !userLongitude) {
                showAlert('warning', 'Please wait for GPS location...');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';

            fetch('{{ route('presences.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: userLatitude,
                    longitude: userLongitude
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', data.error || 'Check-out failed!');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-box-arrow-left"></i> Check Out';
                }
            })
            .catch(error => {
                showAlert('danger', 'Error: ' + error.message);
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-box-arrow-left"></i> Check Out';
            });
        });
    }

    // Refresh location button
    document.getElementById('refreshLocation').addEventListener('click', getUserLocation);

    // Get location on page load
    getUserLocation();
</script>
@endpush
