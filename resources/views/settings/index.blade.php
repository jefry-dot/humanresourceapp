@extends('layouts.dashboard')

@section('title', 'Company Settings')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Company Settings</h3>
                <p class="text-subtitle text-muted">Configure attendance and work schedule settings</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Work Schedule Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock"></i> Work Schedule</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="work_start_time" class="form-label">Work Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('work_start_time') is-invalid @enderror"
                                   id="work_start_time" name="work_start_time"
                                   value="{{ old('work_start_time', substr($settings->work_start_time, 0, 5)) }}" required>
                            @error('work_start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Employees who check-in after this time will be marked as "Late"</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="work_end_time" class="form-label">Work End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('work_end_time') is-invalid @enderror"
                                   id="work_end_time" name="work_end_time"
                                   value="{{ old('work_end_time', substr($settings->work_end_time, 0, 5)) }}" required>
                            @error('work_end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Expected check-out time</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3"><i class="bi bi-geo-alt"></i> Office Location</h6>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>How to get coordinates:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Open <a href="https://www.google.com/maps" target="_blank">Google Maps</a></li>
                            <li>Right-click on your office location</li>
                            <li>Click on the coordinates (e.g., -6.200000, 106.816666) to copy them</li>
                            <li>Paste the latitude and longitude below</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="office_latitude" class="form-label">Office Latitude</label>
                            <input type="number" step="0.000001" class="form-control @error('office_latitude') is-invalid @enderror"
                                   id="office_latitude" name="office_latitude"
                                   value="{{ old('office_latitude', $settings->office_latitude) }}"
                                   placeholder="-6.200000">
                            @error('office_latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="office_longitude" class="form-label">Office Longitude</label>
                            <input type="number" step="0.000001" class="form-control @error('office_longitude') is-invalid @enderror"
                                   id="office_longitude" name="office_longitude"
                                   value="{{ old('office_longitude', $settings->office_longitude) }}"
                                   placeholder="106.816666">
                            @error('office_longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="max_radius_meters" class="form-label">Max Radius (meters) <span class="text-danger">*</span></label>
                            <input type="number" min="10" max="10000" class="form-control @error('max_radius_meters') is-invalid @enderror"
                                   id="max_radius_meters" name="max_radius_meters"
                                   value="{{ old('max_radius_meters', $settings->max_radius_meters) }}" required>
                            @error('max_radius_meters')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Employees must be within this distance to check-in/out</small>
                        </div>
                    </div>

                    <!-- Preview Map -->
                    @if($settings->office_latitude && $settings->office_longitude)
                    <div class="mb-3">
                        <label class="form-label">Office Location Preview</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong>Coordinates:</strong> {{ $settings->office_latitude }}, {{ $settings->office_longitude }}
                                    <a href="https://www.google.com/maps?q={{ $settings->office_latitude }},{{ $settings->office_longitude }}"
                                       target="_blank" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-map"></i> View on Google Maps
                                    </a>
                                </p>
                                <iframe
                                    width="100%"
                                    height="300"
                                    frameborder="0"
                                    style="border:0; border-radius: 5px;"
                                    src="https://www.google.com/maps?q={{ $settings->office_latitude }},{{ $settings->office_longitude }}&output=embed"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Settings Summary -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Current Settings Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Work Start:</th>
                                <td><span class="badge bg-primary">{{ substr($settings->work_start_time, 0, 5) }}</span></td>
                            </tr>
                            <tr>
                                <th>Work End:</th>
                                <td><span class="badge bg-primary">{{ substr($settings->work_end_time, 0, 5) }}</span></td>
                            </tr>
                            <tr>
                                <th>Max Radius:</th>
                                <td><span class="badge bg-info">{{ $settings->max_radius_meters }} meters</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Office Latitude:</th>
                                <td>{{ $settings->office_latitude ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Office Longitude:</th>
                                <td>{{ $settings->office_longitude ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Location Status:</th>
                                <td>
                                    @if($settings->office_latitude && $settings->office_longitude)
                                        <span class="badge bg-success">Configured</span>
                                    @else
                                        <span class="badge bg-warning">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
