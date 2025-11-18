@extends('layouts.admin')

@section('title', 'Division Details')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-3 mb-md-4 gap-2">
        <h1 class="h3 mb-0 text-gray-800">Division: {{ $division->name }}</h1>
        <div class="d-flex gap-2 w-100 w-sm-auto">
            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-secondary btn-sm flex-fill flex-sm-grow-0">
                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- Division Dashboard -->
    <div class="row">
        <div class="col-6 col-xl-3 mb-3 mb-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body p-2 p-md-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmployees }}</div>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3 mb-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body p-2 p-md-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3 mb-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body p-2 p-md-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3 mb-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body p-2 p-md-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Rating</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4
                            </div>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Members -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Division Members</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="mb-3 mb-md-4">
                <h5 class="h6 h-md-5">Division Head</h5>

                @if($divisionHead)
                    <div class="alert alert-info mb-0 p-2 p-md-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                            <div>
                                <strong>{{ $divisionHead->name }}</strong>
                                <div class="small text-muted d-block d-sm-inline"> ({{ $divisionHead->email }})</div>
                            </div>
                            <a href="{{ route('admin.users.edit', $divisionHead) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0 p-2 p-md-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                            <div>No division head assigned</div>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Assign Head
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="h6 h-md-5 mb-0">Employees ({{ count($employees) }})</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add Employee</span>
                    </a>
                </div>

                @if(count($employees) > 0)
                    <!-- Tampilan Mobile (Card) -->
                    <div class="d-block d-lg-none">
                        @foreach($employees as $employee)
                        <div class="card mb-2 border-start border-4 border-primary">
                            <div class="card-body p-2">
                                <h6 class="mb-1 font-weight-bold small">{{ $employee->name }}</h6>
                                <small class="text-muted d-block mb-2">{{ $employee->email }}</small>

                                <div class="row mb-2">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Assigned</small>
                                        <span class="font-weight-bold small">{{ $employee->assigned_jobs_count }}</span>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Completed</small>
                                        <span class="font-weight-bold small">{{ $employee->completed_tasks_count }}</span>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Rate</small>
                                        <span class="font-weight-bold small">
                                            @if($employee->assigned_jobs_count > 0)
                                                {{ number_format(($employee->completed_tasks_count / $employee->assigned_jobs_count) * 100, 0) }}%
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('admin.users.edit', $employee) }}" class="btn btn-info btn-sm w-100">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Tampilan Desktop (Table) -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-bordered table-hover" id="employeesTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 25%;">Name</th>
                                    <th style="width: 25%;">Email</th>
                                    <th style="width: 13%;">Assigned Tasks</th>
                                    <th style="width: 13%;">Completed Tasks</th>
                                    <th style="width: 12%;">Completion Rate</th>
                                    <th style="width: 7%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td class="text-center">{{ $employee->assigned_jobs_count }}</td>
                                    <td class="text-center">{{ $employee->completed_tasks_count }}</td>
                                    <td class="text-center">
                                        @if($employee->assigned_jobs_count > 0)
                                            <span class="badge bg-primary">
                                                {{ number_format(($employee->completed_tasks_count / $employee->assigned_jobs_count) * 100, 0) }}%
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.edit', $employee) }}" class="btn btn-info btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning p-2 p-md-3">
                        No employees assigned to this division.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Division Tasks -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Division Tasks</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row mb-3 mb-md-4">
                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body p-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tasks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body p-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body p-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">In Progress</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @php
                                            $inProgressCount = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                                $query->where('division_id', $division->id);
                                            })->whereNotIn('status', ['final'])->count();
                                        @endphp
                                        {{ $inProgressCount }}
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body p-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Rating</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 mb-md-4">
                <h5 class="h6 h-md-5">Recent Tasks</h5>
                @if(count($recentJobDesks) > 0)
                    <!-- Tampilan Mobile (Card) -->
                    <div class="d-block d-lg-none">
                        @foreach($recentJobDesks as $jobDesk)
                        <div class="card mb-2 border-start border-4 border-primary">
                            <div class="card-body p-2">
                                <h6 class="mb-1 font-weight-bold small">{{ $jobDesk->title }}</h6>
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-user me-1"></i> {{ $jobDesk->creator->name }}
                                </small>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Deadline</small>
                                        <span class="font-weight-bold small">{{ $jobDesk->deadline->format('d M Y') }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Assigned To</small>
                                        <span class="badge bg-secondary small">{{ $jobDesk->assignments->count() }} employees</span>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block">Status</small>
                                    @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                        <span class="badge bg-primary">In Progress</span>
                                    @else
                                        <span class="badge bg-warning">Not Started</span>
                                    @endif
                                </div>

                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> Created: {{ $jobDesk->created_at->format('d M Y') }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Tampilan Desktop (Table) -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-bordered table-hover" id="tasksTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 25%;">Title</th>
                                    <th style="width: 15%;">Created By</th>
                                    <th style="width: 12%;">Deadline</th>
                                    <th style="width: 13%;">Assigned To</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 15%;">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentJobDesks as $jobDesk)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $jobDesk->title }}</td>
                                    <td>{{ $jobDesk->creator->name }}</td>
                                    <td>{{ $jobDesk->deadline->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $jobDesk->assignments->count() }} employees</span>
                                    </td>
                                    <td class="text-center">
                                        @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                            <span class="badge bg-primary">In Progress</span>
                                        @else
                                            <span class="badge bg-warning">Not Started</span>
                                        @endif
                                    </td>
                                    <td>{{ $jobDesk->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning p-2 p-md-3">
                        No tasks created for this division yet.
                    </div>
                @endif
            </div>

            <!-- Task Status Chart -->
            <div class="mt-3 mt-md-4">
                <h5 class="h6 h-md-5 mb-3">Task Status Distribution</h5>
                <div class="chart-container" style="position: relative; height: 250px; height: 300px;">
                    <canvas id="taskStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable hanya untuk tampilan desktop
        if ($(window).width() >= 992) {
            $('#employeesTable').DataTable(
        );

            $('#tasksTable').DataTable(
        );
        }

        // Task Status Chart
        const ctx = document.getElementById('taskStatusChart').getContext('2d');

        // Get task status data
        @php
            $statusCounts = [
                'assigned' => \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                    $query->where('division_id', $division->id);
                })->where('status', 'assigned')->count(),

                'completed' => \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                    $query->where('division_id', $division->id);
                })->where('status', 'completed')->count(),

                'in_review' => \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                    $query->where('division_id', $division->id);
                })->whereIn('status', ['in_review_kadiv', 'kadiv_approved', 'in_review_director'])->count(),

                'rejected' => \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                    $query->where('division_id', $division->id);
                })->whereIn('status', ['rejected_kadiv', 'rejected_director'])->count(),

                'final' => \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                    $query->where('division_id', $division->id);
                })->where('status', 'final')->count(),
            ];
        @endphp

        const taskStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Assigned', 'Completed', 'In Review', 'Rejected', 'Final'],
                datasets: [{
                    data: [
                        {{ $statusCounts['assigned'] }},
                        {{ $statusCounts['completed'] }},
                        {{ $statusCounts['in_review'] }},
                        {{ $statusCounts['rejected'] }},
                        {{ $statusCounts['final'] }}
                    ],
                    backgroundColor: [
                        '#4e73df',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b',
                        '#1cc88a'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9',
                        '#2c9faf',
                        '#dda20a',
                        '#be2617',
                        '#17a673'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: window.innerWidth < 768 ? 'bottom' : 'right',
                        labels: {
                            boxWidth: window.innerWidth < 768 ? 12 : 15,
                            padding: window.innerWidth < 768 ? 8 : 10,
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        titleColor: "#858796",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                }
            },
        });

        // Responsive chart legend on resize
        $(window).resize(function() {
            if (window.innerWidth < 768) {
                taskStatusChart.options.plugins.legend.position = 'bottom';
                taskStatusChart.options.plugins.legend.labels.boxWidth = 12;
                taskStatusChart.options.plugins.legend.labels.padding = 8;
                taskStatusChart.options.plugins.legend.labels.font.size = 10;
            } else {
                taskStatusChart.options.plugins.legend.position = 'right';
                taskStatusChart.options.plugins.legend.labels.boxWidth = 15;
                taskStatusChart.options.plugins.legend.labels.padding = 10;
                taskStatusChart.options.plugins.legend.labels.font.size = 12;
            }
            taskStatusChart.update();
        });
    });
</script>
@endpush