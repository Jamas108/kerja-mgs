@extends('layouts.admin')

@section('title', 'Company Performance Report')

@push('styles')
<style>
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .btn-sm-mobile {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .display-5 {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 1.5rem;
        }

        h3 {
            font-size: 1.25rem;
        }

        h5 {
            font-size: 0.95rem;
        }

        h6 {
            font-size: 0.85rem;
        }

        small {
            font-size: 0.75rem;
        }
    }

    /* Chart responsive */
    .chart-container {
        position: relative;
        height: 300px;
    }

    @media (max-width: 767.98px) {
        .chart-container {
            height: 250px;
        }
    }

    /* Stat cards mobile */
    @media (max-width: 767.98px) {
        .col-6 .card-body {
            padding: 0.75rem;
        }
    }

    /* Print styles */
    @media print {
        .btn,
        .page-header .d-flex > div:last-child {
            display: none !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
        }

        body {
            padding: 20px !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }

        .chart-container {
            height: 300px !important;
        }

        h1:first-of-type {
            margin-top: 0 !important;
        }

        .row {
            page-break-inside: avoid;
        }

        /* Force desktop layout for print */
        .d-lg-none {
            display: none !important;
        }

        .d-none.d-lg-flex {
            display: flex !important;
        }
    }

    /* Division performance cards */
    .division-card {
        transition: transform 0.2s;
    }

    .division-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 mb-2 text-gray-800 fw-bold">Company Performance Report</h2>
                <p class="text-secondary mb-0">Comprehensive analysis of employee performance across all divisions</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.performances.index') }}" class="btn btn-secondary btn-sm-mobile">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
                <button class="btn btn-primary btn-sm-mobile" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.performances.report') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.performances.report') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Statistics -->
    <div class="row mb-4">
        <!-- Total Employees -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-header-title mb-0">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Employee Overview
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="display-5 mb-2">{{ $totalEmployees }}</h2>
                            <p class="text-muted mb-0">Total Employees</p>
                        </div>
                        <div class="col-6">
                            <h2 class="display-5 mb-2">{{ $totalEmployees - $performanceStats['no_rating'] }}</h2>
                            <p class="text-muted mb-0">With Ratings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Distribution -->
        <div class="col-md-8 mb-3 mb-md-0">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-header-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>
                        Performance Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Statistics -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Performance Statistics
            </h5>
        </div>
        <div class="card-body">
            <!-- Desktop View -->
            <div class="row g-3 d-none d-lg-flex">
                <div class="col">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Excellent</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['excellent'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['excellent'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-info text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Good</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['good'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['good'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Average</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['average'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['average'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-warning shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Below Average</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['below_average'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['below_average'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-danger text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Poor</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['poor'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['poor'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile/Tablet View -->
            <div class="row g-3 d-lg-none">
                <div class="col-6 col-md-4">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Excellent</h6>
                            <h3 class="mb-2">{{ $performanceStats['excellent'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['excellent'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Good</h6>
                            <h3 class="mb-2">{{ $performanceStats['good'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['good'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Average</h6>
                            <h3 class="mb-2">{{ $performanceStats['average'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['average'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-warning shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Below Average</h6>
                            <h3 class="mb-2">{{ $performanceStats['below_average'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['below_average'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Poor</h6>
                            <h3 class="mb-2">{{ $performanceStats['poor'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['poor'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-secondary text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">No Rating</h6>
                            <h3 class="mb-2">{{ $performanceStats['no_rating'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['no_rating'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-chart-line me-2 text-success"></i>
                Performance Trends
            </h5>
        </div>
        <div class="card-body">
            @if (count($monthlyPerformance) > 0)
                <div class="chart-container">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0">No performance trend data available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Division Performance Breakdown -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-building me-2 text-warning"></i>
                Division Performance Overview
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($divisionStats as $division)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card division-card h-100 border-left-primary">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">{{ $division->name }}</h6>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h4 class="text-dark">{{ $division->total_employees }}</h4>
                                        <small class="text-muted">Total Employees</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        @php
                                            $ratedEmployees = $division->total_employees - $division->performance_stats['no_rating'];
                                        @endphp
                                        <h4 class="text-success">{{ $ratedEmployees }}</h4>
                                        <small class="text-muted">With Ratings</small>
                                    </div>
                                </div>
                            </div>

                            <div class="performance-breakdown">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success">Excellent</span>
                                    <span>{{ $division->performance_stats['excellent'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-info">Good</span>
                                    <span>{{ $division->performance_stats['good'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">Average</span>
                                    <span>{{ $division->performance_stats['average'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-warning">Below Avg</span>
                                    <span>{{ $division->performance_stats['below_average'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-danger">Poor</span>
                                    <span>{{ $division->performance_stats['poor'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Promotion Statistics -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-award me-2 text-warning"></i>
                Promotion Statistics
            </h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h3 class="text-dark">{{ $promotionStats['pending'] }}</h3>
                            <p class="mb-0">Pending Promotions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h3>{{ $promotionStats['approved'] }}</h3>
                            <p class="mb-0">Approved Promotions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h3>{{ $promotionStats['rejected'] }}</h3>
                            <p class="mb-0">Rejected Promotions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Detect if mobile
        const isMobile = window.innerWidth < 768;

        // Performance distribution chart
        const performanceStats = @json($performanceStats);
        const ctxDistribution = document.getElementById('performanceDistribution').getContext('2d');

        new Chart(ctxDistribution, {
            type: 'doughnut',
            data: {
                labels: ['Excellent', 'Good', 'Average', 'Below Average', 'Poor', 'No Rating'],
                datasets: [{
                    data: [
                        performanceStats.excellent,
                        performanceStats.good,
                        performanceStats.average,
                        performanceStats.below_average,
                        performanceStats.poor,
                        performanceStats.no_rating
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgb(40, 167, 69)',
                        'rgb(23, 162, 184)',
                        'rgb(0, 123, 255)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(108, 117, 125)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'right',
                        labels: {
                            font: {
                                size: isMobile ? 10 : 12
                            },
                            padding: isMobile ? 10 : 15
                        }
                    }
                }
            }
        });

        // Monthly trends chart
        const monthlyData = @json($monthlyPerformance);

        if (monthlyData.length > 0) {
            const ctxTrends = document.getElementById('monthlyTrendsChart').getContext('2d');

            const labels = monthlyData.map(item => item.period);
            const avgScores = monthlyData.map(item => item.average_score);
            const totalTasks = monthlyData.map(item => item.total_tasks);
            const totalEmployees = monthlyData.map(item => item.total_employees);

            new Chart(ctxTrends, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Average Performance',
                            data: avgScores,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            yAxisID: 'y',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Total Tasks',
                            data: totalTasks,
                            borderColor: 'rgb(153, 102, 255)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            yAxisID: 'y1',
                            type: 'bar'
                        },
                        {
                            label: 'Active Employees',
                            data: totalEmployees,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            yAxisID: 'y2',
                            type: 'bar'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            min: 1,
                            max: 4,
                            title: {
                                display: !isMobile,
                                text: 'Performance Rating',
                                font: {
                                    size: isMobile ? 10 : 12
                                }
                            },
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 0,
                            title: {
                                display: !isMobile,
                                text: 'Tasks',
                                font: {
                                    size: isMobile ? 10 : 12
                                }
                            },
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: !isMobile,
                            position: 'right',
                            min: 0,
                            title: {
                                display: true,
                                text: 'Employees',
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: isMobile ? 'bottom' : 'top',
                            labels: {
                                font: {
                                    size: isMobile ? 10 : 12
                                },
                                padding: isMobile ? 10 : 15
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection