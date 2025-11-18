@extends('layouts.admin')

@section('title', 'Employee Performance Details')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

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

        /* Profile card adjustments */
        .profile-img {
            width: 80px !important;
            height: 80px !important;
        }

        .profile-score {
            font-size: 2.5rem !important;
        }

        .card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .display-4 {
            font-size: 2rem;
        }

        .lead {
            font-size: 1rem;
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

    /* Table mobile view */
    .task-card-mobile {
        border-left: 4px solid #4e73df;
    }

    .badge-mobile {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    /* Profile card center on mobile */
    @media (max-width: 767.98px) {
        .profile-card-body {
            padding: 1.5rem 1rem;
        }
    }

    /* Promotion section styling */
    .promotion-card {
        border-left: 4px solid #28a745;
    }

    .promotion-pending {
        border-left: 4px solid #ffc107;
    }

    .promotion-history {
        border-left: 4px solid #6c757d;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="flex-grow-1">
            <h2 class="h3 mb-2 text-gray-800 fw-bold">Employee Performance Details</h2>
            <p class="text-secondary mb-0">Detailed performance analysis and evaluation</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.performances.index') }}" class="btn btn-secondary btn-sm-mobile">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            @if ($performanceScore >= 3 && !$promotionRequest)
                <a href="{{ route('admin.performances.propose_promotion', $employee->id) }}" class="btn btn-success btn-sm-mobile">
                    <i class="fas fa-award me-2"></i>Propose Promotion
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Employee Profile -->
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-header-title mb-0">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Employee Profile
                </h5>
            </div>
            <div class="card-body text-center profile-card-body">
                <img src="https://ui-avatars.com/api/?name={{ $employee->name }}&background=random&size=100"
                     class="rounded-circle mb-3 profile-img"
                     alt="{{ $employee->name }}"
                     width="100"
                     height="100">
                <h4 class="fw-bold mb-1">{{ $employee->name }}</h4>
                <p class="text-muted mb-2">{{ $employee->email }}</p>
                <span class="badge bg-info mb-3">{{ $employee->division->name ?? 'No Division' }}</span>

                @if ($performanceScore)
                    <div class="mt-4">
                        <h2 class="display-4 profile-score mb-0">{{ $performanceScore }}</h2>
                        <p class="lead">out of 4.00</p>

                        @php
                            $badgeClass = 'bg-danger';

                            if ($performanceScore >= 3.7) {
                                $badgeClass = 'bg-success';
                            } elseif ($performanceScore >= 3) {
                                $badgeClass = 'bg-info';
                            } elseif ($performanceScore >= 2.5) {
                                $badgeClass = 'bg-primary';
                            } elseif ($performanceScore >= 2) {
                                $badgeClass = 'bg-warning';
                            }
                        @endphp

                        <h4><span class="badge {{ $badgeClass }} px-3 py-2">{{ $performanceCategory }}</span></h4>
                    </div>
                @else
                    <div class="mt-4">
                        <h4><span class="badge bg-secondary px-3 py-2">No Performance Rating</span></h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Performance Statistics -->
    <div class="col-md-8 mb-3 mb-md-0">
        <div class="card shadow h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-header-title mb-0">
                    <i class="fas fa-chart-line me-2 text-info"></i>
                    Monthly Performance Statistics
                </h5>
            </div>
            <div class="card-body">
                @if (count($monthlyPerformance) > 0)
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                        <div class="alert-icon me-3">
                            <i class="fas fa-info-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="mb-0">No monthly performance data available for this employee.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Promotion Information -->
@if($promotionRequest || count($promotionHistory) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-white py-3">
                <h5 class="card-header-title mb-0">
                    <i class="fas fa-award me-2 text-warning"></i>
                    Promotion Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($promotionRequest)
                    <!-- Pending Promotion Request -->
                    <div class="col-md-6 mb-3">
                        <div class="card promotion-pending">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    Pending Promotion Request
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Period:</strong> {{ $promotionRequest->period }}
                                </div>
                                <div class="mb-2">
                                    <strong>Requested by:</strong> {{ $promotionRequest->requestedBy->name ?? 'Admin' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Request Date:</strong> {{ $promotionRequest->created_at->format('d M Y') }}
                                </div>
                                @if($promotionRequest->supporting_document)
                                <div class="mb-2">
                                    <strong>Supporting Document:</strong>
                                    <a href="{{ Storage::url($promotionRequest->supporting_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                                @endif
                                <div>
                                    <strong>Reason:</strong>
                                    <p class="mt-1">{{ $promotionRequest->reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($promotionHistory) > 0)
                    <!-- Promotion History -->
                    <div class="col-md-{{ $promotionRequest ? '6' : '12' }} mb-3">
                        <div class="card promotion-history">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Promotion History
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($promotionHistory as $history)
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>{{ $history->period }}</strong>
                                        <span class="badge {{ $history->status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        Requested by {{ $history->requestedBy->name ?? 'Admin' }} on {{ $history->created_at->format('d M Y') }}
                                    </small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Task List and Ratings -->
<div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-tasks me-2 text-primary"></i>
                Task List and Performance Ratings
            </h5>
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-clipboard-check me-1"></i>
                {{ count($ratedAssignments) }} Total Tasks
            </span>
        </div>
    </div>
    <div class="card-body">
        <!-- Desktop View -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover" id="employee-tasks-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Task Title</th>
                        <th>Division</th>
                        <th>Status</th>
                        <th>Head Rating</th>
                        <th>Director Rating</th>
                        <th>Average Rating</th>
                        <th>Review Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ratedAssignments as $assignment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $assignment->jobDesk->title }}</td>
                            <td>
                                <span class="badge bg-info">{{ $assignment->jobDesk->division->name ?? 'No Division' }}</span>
                            </td>
                            <td>{!! $assignment->status_badge !!}</td>
                            <td>
                                @if ($assignment->kadiv_rating)
                                    <span class="badge bg-primary">{{ $assignment->kadiv_rating }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($assignment->director_rating)
                                    <span class="badge bg-info">{{ $assignment->director_rating }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($assignment->kadiv_rating && $assignment->director_rating)
                                    @php
                                        $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
                                        $badgeClass = 'bg-danger';

                                        if ($avgRating >= 3.7) {
                                            $badgeClass = 'bg-success';
                                        } elseif ($avgRating >= 3) {
                                            $badgeClass = 'bg-info';
                                        } elseif ($avgRating >= 2.5) {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($avgRating >= 2) {
                                            $badgeClass = 'bg-warning';
                                        }
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">{{ number_format($avgRating, 2) }}</span>
                                @elseif ($assignment->kadiv_rating)
                                    <span class="badge bg-warning text-dark">Pending director</span>
                                @else
                                    <span class="badge bg-secondary">Not rated</span>
                                @endif
                            </td>
                            <td>
                                @if ($assignment->status == 'final' && $assignment->director_reviewed_at)
                                    <span class="badge bg-light text-dark">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $assignment->director_reviewed_at->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">No rated tasks available</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile/Tablet View -->
        <div class="d-lg-none">
            @forelse ($ratedAssignments as $assignment)
                <div class="card mb-3 task-card-mobile shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="fw-bold text-primary mb-1">{{ $assignment->jobDesk->title }}</h6>
                                <span class="badge bg-info">{{ $assignment->jobDesk->division->name ?? 'No Division' }}</span>
                            </div>
                            <span class="badge bg-secondary badge-mobile">{{ $loop->iteration }}</span>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Status</small>
                            {!! $assignment->status_badge !!}
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Head Rating</small>
                                @if ($assignment->kadiv_rating)
                                    <span class="badge bg-primary badge-mobile">{{ $assignment->kadiv_rating }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Director Rating</small>
                                @if ($assignment->director_rating)
                                    <span class="badge bg-info badge-mobile">{{ $assignment->director_rating }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Average Rating</small>
                            @if ($assignment->kadiv_rating && $assignment->director_rating)
                                @php
                                    $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
                                    $badgeClass = 'bg-danger';

                                    if ($avgRating >= 3.7) {
                                        $badgeClass = 'bg-success';
                                    } elseif ($avgRating >= 3) {
                                        $badgeClass = 'bg-info';
                                    } elseif ($avgRating >= 2.5) {
                                        $badgeClass = 'bg-primary';
                                    } elseif ($avgRating >= 2) {
                                        $badgeClass = 'bg-warning';
                                    }
                                @endphp

                                <span class="badge {{ $badgeClass }} badge-mobile">{{ number_format($avgRating, 2) }}</span>
                            @elseif ($assignment->kadiv_rating)
                                <span class="badge bg-warning text-dark badge-mobile">Pending director</span>
                            @else
                                <span class="badge bg-secondary badge-mobile">Not rated</span>
                            @endif
                        </div>

                        <div>
                            <small class="text-muted d-block mb-1">Review Date</small>
                            @if ($assignment->status == 'final' && $assignment->director_reviewed_at)
                                <span class="badge bg-light text-dark badge-mobile">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $assignment->director_reviewed_at->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0">No rated tasks available</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

@if (count($monthlyPerformance) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable only on desktop
        if ($(window).width() >= 992) {
            $('#employee-tasks-table').DataTable(
            //     {
            //     "language": {
            //         "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
            //     },
            //     "responsive": true,
            //     "pageLength": 10,
            //     "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            //     "search": {
            //         "smart": true,
            //         "caseInsensitive": true
            //     },
            //     "ordering": true,
            //     "info": true,
            //     "autoWidth": false,
            //     "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            //     "initComplete": function() {
            //         // Customize search input
            //         $('.dataTables_filter input').attr('placeholder', 'Search tasks...');
            //         $('.dataTables_filter input').addClass('form-control');
            //         $('.dataTables_filter label').addClass('mb-0');

            //         // Customize length menu
            //         $('.dataTables_length select').addClass('form-select form-select-sm');
            //     }
            // }
        );
        }

        // Initialize Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');

        // Data from controller
        const performanceData = @json($monthlyPerformance);

        const labels = performanceData.map(item => item.period);
        const scores = performanceData.map(item => item.average_score);
        const tasks = performanceData.map(item => item.total_tasks);

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Average Rating',
                        data: scores,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        yAxisID: 'y',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Total Tasks',
                        data: tasks,
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        yAxisID: 'y1',
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
                            display: true,
                            text: 'Performance Rating'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 0,
                        title: {
                            display: true,
                            text: 'Number of Tasks'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: window.innerWidth < 768 ? 'bottom' : 'top'
                    }
                }
            }
        });
    });
</script>
@else
<script>
    $(document).ready(function() {
        // Initialize DataTable only on desktop
        if ($(window).width() >= 992) {
            $('#employee-tasks-table').DataTable(
            //     {
            //     "language": {
            //         "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
            //     },
            //     "responsive": true,
            //     "pageLength": 10,
            //     "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            //     "search": {
            //         "smart": true,
            //         "caseInsensitive": true
            //     },
            //     "ordering": true,
            //     "info": true,
            //     "autoWidth": false,
            //     "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            //     "initComplete": function() {
            //         // Customize search input
            //         $('.dataTables_filter input').attr('placeholder', 'Search tasks...');
            //         $('.dataTables_filter input').addClass('form-control');
            //         $('.dataTables_filter label').addClass('mb-0');

            //         // Customize length menu
            //         $('.dataTables_length select').addClass('form-select form-select-sm');
            //     }
            // }
        );
        }
    });
</script>
@endif
@endpush
@endsection