@extends('layouts.admin')

@section('title', 'Review Statistics')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Statistics</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reviews
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Statistics</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reviews.statistics') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="division_id" class="form-label">Division</label>
                        <select name="division_id" id="division_id" class="form-control">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.reviews.statistics') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assignments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statusStats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Finalized</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statusStats['final']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Reviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statusStats['in_review_kadiv'] + $statusStats['in_review_director']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statusStats['rejected_kadiv'] + $statusStats['rejected_director']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Status Breakdown Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assignment Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Director Rating Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ratingChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> 1 (Poor)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> 2 (Fair)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> 3 (Good)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> 4 (Excellent)
                        </span>
                    </div>
                    <div class="mt-3 text-center">
                        <div class="small">
                            <strong>Average Director Rating:</strong>
                            {{ number_format($ratingStats['director_avg'], 2) }} / 4
                        </div>
                        <div class="small">
                            <strong>Average Head Rating:</strong>
                            {{ number_format($ratingStats['kadiv_avg'], 2) }} / 4
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trends -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Assignment Trends</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Division Performance -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Division Performance</h6>
                </div>
                <div class="card-body">
                    @foreach($divisionStats as $division)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">{{ $division->name }}</span>
                            <span class="text-sm">{{ $division->completed_assignments }}/{{ $division->total_assignments }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $percentage = $division->total_assignments > 0 ?
                                            ($division->completed_assignments / $division->total_assignments) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $percentage }}%"
                                 aria-valuenow="{{ $percentage }}"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ number_format($percentage, 1) }}% completion rate</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Status Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detailed Status Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Percentage</th>
                            <th>Progress Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusLabels = [
                                'assigned' => 'Assigned',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'in_review_kadiv' => 'Head Review',
                                'in_review_director' => 'Director Review',
                                'rejected_kadiv' => 'Rejected by Head',
                                'rejected_director' => 'Rejected by Director',
                                'final' => 'Final'
                            ];

                            $statusColors = [
                                'assigned' => 'secondary',
                                'in_progress' => 'primary',
                                'completed' => 'info',
                                'in_review_kadiv' => 'warning',
                                'in_review_director' => 'warning',
                                'rejected_kadiv' => 'danger',
                                'rejected_director' => 'danger',
                                'final' => 'success'
                            ];
                        @endphp

                        @foreach($statusLabels as $status => $label)
                        @php
                            $count = $statusStats[$status];
                            $percentage = $statusStats['total'] > 0 ? ($count / $statusStats['total']) * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-{{ $statusColors[$status] }}">{{ $label }}</span>
                            </td>
                            <td>{{ number_format($count) }}</td>
                            <td>{{ number_format($percentage, 1) }}%</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $statusColors[$status] }}"
                                         role="progressbar" style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Assigned', 'In Progress', 'Completed', 'Head Review', 'Director Review', 'Rejected Head', 'Rejected Director', 'Final'],
            datasets: [{
                data: [
                    {{ $statusStats['assigned'] }},
                    {{ $statusStats['in_progress'] }},
                    {{ $statusStats['completed'] }},
                    {{ $statusStats['in_review_kadiv'] }},
                    {{ $statusStats['in_review_director'] }},
                    {{ $statusStats['rejected_kadiv'] }},
                    {{ $statusStats['rejected_director'] }},
                    {{ $statusStats['final'] }}
                ],
                backgroundColor: [
                    '#6c757d', '#4e73df', '#17a2b8', '#ffc107',
                    '#fd7e14', '#dc3545', '#e74c3c', '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Rating Distribution Chart
    const ratingCtx = document.getElementById('ratingChart').getContext('2d');
    new Chart(ratingCtx, {
        type: 'pie',
        data: {
            labels: ['1 (Poor)', '2 (Fair)', '3 (Good)', '4 (Excellent)'],
            datasets: [{
                data: [
                    {{ $ratingStats['rating_distribution']['1'] }},
                    {{ $ratingStats['rating_distribution']['2'] }},
                    {{ $ratingStats['rating_distribution']['3'] }},
                    {{ $ratingStats['rating_distribution']['4'] }}
                ],
                backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#28a745']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyStats, 'month')) !!},
            datasets: [{
                label: 'Created',
                data: {!! json_encode(array_column($monthlyStats, 'created')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3