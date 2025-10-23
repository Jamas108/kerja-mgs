@extends('layouts.employee')

@section('title', 'Employee Dashboard')

@section('content')
<!-- Dashboard Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Welcome Back, {{ auth()->user()->name }}!</h2>
        <p class="text-secondary mb-0">Here's your performance overview for today, {{ date('F d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light">
            <i class="fas fa-calendar me-2"></i>This Week
        </button>
        <a href="{{ route('employee.tasks.index') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>View All Tasks
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- Pending Tasks Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-stat">
                <div class="card-stat-background"></div>
                <div class="card-stat-icon primary">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-stat-content">
                    <div class="card-stat-title">Pending Tasks</div>
                    <h2 class="card-stat-value">{{ $pendingTasks->count() }}
                        <span class="card-stat-value-trend up">
                            <i class="fas fa-arrow-up me-1"></i>4%
                        </span>
                    </h2>
                    <p class="card-stat-description">Tasks waiting to be completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tasks Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-stat">
                <div class="card-stat-background"></div>
                <div class="card-stat-icon info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-stat-content">
                    <div class="card-stat-title">Completed Tasks</div>
                    <h2 class="card-stat-value">{{ $completedTasks->count() }}
                        <span class="card-stat-value-trend up">
                            <i class="fas fa-arrow-up me-1"></i>12%
                        </span>
                    </h2>
                    <p class="card-stat-description">Tasks awaiting approval</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected Tasks Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-stat">
                <div class="card-stat-background"></div>
                <div class="card-stat-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="card-stat-content">
                    <div class="card-stat-title">Rejected Tasks</div>
                    <h2 class="card-stat-value">{{ $rejectedTasks->count() }}
                        <span class="card-stat-value-trend down">
                            <i class="fas fa-arrow-down me-1"></i>2%
                        </span>
                    </h2>
                    <p class="card-stat-description">Tasks needing revision</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Finished Tasks Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-stat">
                <div class="card-stat-background"></div>
                <div class="card-stat-icon success">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="card-stat-content">
                    <div class="card-stat-title">Finished Tasks</div>
                    <h2 class="card-stat-value">{{ $finishedTasks->count() }}
                        <span class="card-stat-value-trend up">
                            <i class="fas fa-arrow-up me-1"></i>8%
                        </span>
                    </h2>
                    <p class="card-stat-description">Successfully completed tasks</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Overview & Activity Summary -->
<div class="row g-4 mb-4">
    <!-- Performance Overview Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Performance Overview
                </h5>
                <div class="card-header-actions">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-light active">Weekly</button>
                        <button type="button" class="btn btn-sm btn-light">Monthly</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Recent Activity
                </h5>
                <div class="card-header-actions">
                    <button class="btn btn-sm btn-icon btn-light">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 d-flex align-items-center px-4 py-3">
                        <div class="me-3">
                            <div class="avatar-stack-item bg-primary-light text-primary">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Task completed</p>
                            <p class="text-secondary small mb-0">You completed "Q3 Sales Report"</p>
                        </div>
                        <div class="text-end text-secondary small">
                            2h ago
                        </div>
                    </div>
                    <div class="list-group-item border-0 d-flex align-items-center px-4 py-3">
                        <div class="me-3">
                            <div class="avatar-stack-item bg-danger-light text-danger">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Task rejected</p>
                            <p class="text-secondary small mb-0">"Website Redesign" needs revision</p>
                        </div>
                        <div class="text-end text-secondary small">
                            Yesterday
                        </div>
                    </div>
                    <div class="list-group-item border-0 d-flex align-items-center px-4 py-3">
                        <div class="me-3">
                            <div class="avatar-stack-item bg-info-light text-info">
                                <i class="fas fa-comment"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">New comment</p>
                            <p class="text-secondary small mb-0">Sarah commented on your task</p>
                        </div>
                        <div class="text-end text-secondary small">
                            Yesterday
                        </div>
                    </div>
                    <div class="list-group-item border-0 d-flex align-items-center px-4 py-3">
                        <div class="me-3">
                            <div class="avatar-stack-item bg-success-light text-success">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">New task assigned</p>
                            <p class="text-secondary small mb-0">You've been assigned "Market Analysis"</p>
                        </div>
                        <div class="text-end text-secondary small">
                            2 days ago
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 text-center">
                    <a href="#" class="text-primary fw-medium text-decoration-none">View All Activity <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tasks Overview -->
<div class="row g-4">
    <!-- Pending Tasks -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>
                    Pending Tasks
                </h5>
                <div class="card-header-actions">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">All Tasks</a></li>
                            <li><a class="dropdown-item" href="#">High Priority</a></li>
                            <li><a class="dropdown-item" href="#">Due This Week</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($pendingTasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Priority</th>
                                    <th>Deadline</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingTasks as $task)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $task->jobDesk->title }}</span>
                                    </td>
                                    <td>{{ Str::limit($task->jobDesk->description, 50) }}</td>
                                    <td>
                                        <span class="badge bg-danger-light text-danger">High</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ \Carbon\Carbon::parse($task->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $task->jobDesk->deadline->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 100px;">
                                                <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2 text-secondary small">65%</span>
                                        </div>
                                    </td>
                                    <td>{!! $task->status_badge !!}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <div class="alert-icon me-3">
                            <i class="fas fa-info-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="fw-medium mb-0">No pending tasks at this time</p>
                            <p class="mb-0 small">You're all caught up! Check back later for new assignments.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rejected Tasks -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                    Tasks Needing Revision
                </h5>
                <div class="card-header-actions">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="rejectedDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="rejectedDropdownMenu">
                            <li><a class="dropdown-item" href="#">All Revisions</a></li>
                            <li><a class="dropdown-item" href="#">From Division Head</a></li>
                            <li><a class="dropdown-item" href="#">From Director</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($rejectedTasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Deadline</th>
                                    <th>Rejected By</th>
                                    <th>Feedback</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rejectedTasks as $task)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $task->jobDesk->title }}</span>
                                    </td>
                                    <td>{{ Str::limit($task->jobDesk->description, 50) }}</td>
                                    <td>
                                        <span class="badge {{ \Carbon\Carbon::parse($task->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $task->jobDesk->deadline->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-stack-item me-2">
                                                {{ substr($task->status == 'rejected_kadiv' ? $task->jobDesk->division->kadiv->name : $task->jobDesk->division->director->name, 0, 1) }}
                                            </div>
                                            <span>{{ $task->status == 'rejected_kadiv' ? 'Division Head' : 'Director' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $task->status == 'rejected_kadiv' ? ($task->kadiv_notes ?? 'No feedback provided.') : ($task->director_notes ?? 'No feedback provided.') }}">
                                            <i class="fas fa-comment-dots me-1"></i> View Feedback
                                        </button>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i> Revise
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <div class="alert-icon me-3">
                            <i class="fas fa-check-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="fw-medium mb-0">Great job! No revisions needed</p>
                            <p class="mb-0 small">You don't have any tasks that need revision at this time.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Deadlines -->
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-clock me-2 text-warning"></i>
                    Upcoming Deadlines
                </h5>
                <div class="card-header-actions">
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="fas fa-calendar me-1"></i> View Calendar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-item-date">
                            <div class="timeline-date-day">15</div>
                            <div class="timeline-date-month">Aug</div>
                        </div>
                        <div class="timeline-item-content">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="timeline-item-title">Q3 Financial Report</h6>
                                <span class="badge bg-danger-light text-danger">2 days left</span>
                            </div>
                            <p class="timeline-item-text">Complete the quarterly financial analysis and prepare presentation</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar-stack">
                                    <div class="avatar-stack-item" style="background-color: #4f46e5; color: white;">JD</div>
                                    <div class="avatar-stack-item" style="background-color: #10b981; color: white;">AS</div>
                                    <div class="avatar-stack-item" style="background-color: #f59e0b; color: white;">RK</div>
                                </div>
                                <span class="ms-2 text-secondary small">3 collaborators</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-item-date">
                            <div class="timeline-date-day">18</div>
                            <div class="timeline-date-month">Aug</div>
                        </div>
                        <div class="timeline-item-content">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="timeline-item-title">Marketing Campaign Proposal</h6>
                                <span class="badge bg-warning-light text-warning">5 days left</span>
                            </div>
                            <p class="timeline-item-text">Draft the Q4 marketing strategy and budget allocation</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar-stack">
                                    <div class="avatar-stack-item" style="background-color: #0ea5e9; color: white;">TW</div>
                                    <div class="avatar-stack-item" style="background-color: #ef4444; color: white;">MJ</div>
                                </div>
                                <span class="ms-2 text-secondary small">2 collaborators</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-item-date">
                            <div class="timeline-date-day">22</div>
                            <div class="timeline-date-month">Aug</div>
                        </div>
                        <div class="timeline-item-content">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="timeline-item-title">Client Presentation</h6>
                                <span class="badge bg-primary-light text-primary">9 days left</span>
                            </div>
                            <p class="timeline-item-text">Prepare slides for the upcoming client presentation</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar-stack">
                                    <div class="avatar-stack-item" style="background-color: #4f46e5; color: white;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                </div>
                                <span class="ms-2 text-secondary small">Only you</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 60px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--border-color);
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item:last-child:before {
        bottom: 50%;
    }

    .timeline-item-date {
        position: absolute;
        left: -60px;
        top: 0;
        width: 45px;
        height: 45px;
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        z-index: 2;
    }

    .timeline-date-day {
        font-size: 16px;
        line-height: 1;
    }

    .timeline-date-month {
        font-size: 12px;
        line-height: 1;
    }

    .timeline-item-content {
        background-color: var(--light-color);
        border-radius: 12px;
        padding: 20px;
        position: relative;
    }

    .timeline-item-content:before {
        content: '';
        position: absolute;
        left: -6px;
        top: 20px;
        width: 12px;
        height: 12px;
        background-color: var(--light-color);
        transform: rotate(45deg);
    }

    .timeline-item-title {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .timeline-item-text {
        color: var(--secondary-color);
        font-size: 14px;
        margin-bottom: 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Performance Chart
        var ctx = document.getElementById('performanceChart').getContext('2d');
        var performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [
                    {
                        label: 'Completed Tasks',
                        data: [3, 5, 2, 6, 4, 3, 5],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Pending Tasks',
                        data: [1, 3, 4, 2, 5, 3, 2],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8,
                        caretSize: 6
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 8,
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection