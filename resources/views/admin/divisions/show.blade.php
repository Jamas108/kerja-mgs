@extends('layouts.admin')

@section('title', 'Division Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Division: {{ $division->name }}</h1>
        <div>
            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Division
            </a>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Divisions
            </a>
        </div>
    </div>

    <!-- Division Dashboard -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmployees }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Total Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Average Rating</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Members -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Division Members</h6>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <h5>Division Head</h5>

                @if($divisionHead)
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $divisionHead->name }}</strong> ({{ $divisionHead->email }})
                            </div>
                            <a href="{{ route('admin.users.edit', $divisionHead) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>No division head assigned</div>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Assign Head
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <h5>Employees ({{ count($employees) }})</h5>
                @if(count($employees) > 0)
                    <table class="table table-bordered" width="100%" cellspacing="0" id="employeesTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Assigned Tasks</th>
                                <th>Completed Tasks</th>
                                <th>Completion Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->assigned_jobs_count }}</td>
                                <td>{{ $employee->completed_tasks_count }}</td>
                                <td>
                                    @if($employee->assigned_jobs_count > 0)
                                        {{ number_format(($employee->completed_tasks_count / $employee->assigned_jobs_count) * 100, 0) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $employee) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">
                        No employees assigned to this division.
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Employee
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Tasks -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Division Tasks</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tasks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
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
                                <div class="col-auto">
                                    <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Rating</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <h5>Recent Tasks</h5>
                @if(count($recentJobDesks) > 0)
                    <table class="table table-bordered" width="100%" cellspacing="0" id="tasksTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created By</th>
                                <th>Deadline</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentJobDesks as $jobDesk)
                            <tr>
                                <td>{{ $jobDesk->title }}</td>
                                <td>{{ $jobDesk->creator->name }}</td>
                                <td>{{ $jobDesk->deadline->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $jobDesk->assignments->count() }} employees</span>
                                </td>
                                <td>
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
                @else
                    <div class="alert alert-warning">
                        No tasks created for this division yet.
                    </div>
                @endif
            </div>

            <!-- Task Status Chart -->
            <div class="mt-4">
                <h5>Task Status Distribution</h5>
                <div>
                    <canvas id="taskStatusChart" height="200"></canvas>
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
        $('#employeesTable').DataTable(
    //         {
    //         pageLength: 10,
    //         order: [[4, 'desc']] // Sort by completion rate by default
    //     }
    // );

        $('#tasksTable').DataTable({
            pageLength: 10,
            order: [[5, 'desc']] // Sort by created at by default
        });

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
                labels: ['Assigned', 'Completed (Waiting Review)', 'In Review', 'Rejected', 'Final'],
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
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 0,
            },
        });
    });
</script>
@endpush