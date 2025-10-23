<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users }}</div>
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
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $finalTasks->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Pending Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Completed Tasks</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Task Title</th>
                                    <th>Employee</th>
                                    <th>Division</th>
                                    <th>Kadiv Rating</th>
                                    <th>Director Rating</th>
                                    <th>Completed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($finalTasks as $task)
                                <tr>
                                    <td>{{ $task->jobDesk->title }}</td>
                                    <td>{{ $task->employee->name }}</td>
                                    <td>{{ $task->employee->division->name ?? 'N/A' }}</td>
                                    <td>{{ $task->kadiv_rating }} / 4</td>
                                    <td>{{ $task->director_rating }} / 4</td>
                                    <td>{{ $task->completed_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Division Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Division</th>
                                    <th>Employees</th>
                                    <th>Tasks</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Division::all() as $division)
                                <tr>
                                    <td>{{ $division->name }}</td>
                                    <td>{{ $division->users()->where('role_id', 1)->count() }}</td>
                                    <td>{{ $division->jobDesks()->count() }}</td>
                                    <td>
                                        {{ \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                            $query->where('division_id', $division->id);
                                        })->where('status', 'final')->count() }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Employee</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\EmployeeJobDesk::with(['employee', 'jobDesk'])->orderBy('updated_at', 'desc')->take(5)->get() as $activity)
                                <tr>
                                    <td>{{ $activity->jobDesk->title }}</td>
                                    <td>{{ $activity->employee->name }}</td>
                                    <td>{!! $activity->status_badge !!}</td>
                                    <td>{{ $activity->updated_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">System Performance Summary</h4>
                        <p>Total completed tasks: <strong>{{ $finalTasks->count() }}</strong></p>
                        <p>Average Kadiv rating: <strong>{{ $finalTasks->avg('kadiv_rating') ? number_format($finalTasks->avg('kadiv_rating'), 2) : 'N/A' }}/4</strong></p>
                        <p>Average Director rating: <strong>{{ $finalTasks->avg('director_rating') ? number_format($finalTasks->avg('director_rating'), 2) : 'N/A' }}/4</strong></p>
                        <p>Average completion time: <strong>
                            @php
                                $avgTime = 0;
                                $count = 0;
                                foreach($finalTasks as $task) {
                                    if ($task->completed_at && $task->jobDesk && $task->jobDesk->created_at) {
                                        $diff = $task->completed_at->diffInDays($task->jobDesk->created_at);
                                        $avgTime += $diff;
                                        $count++;
                                    }
                                }
                                echo $count > 0 ? number_format($avgTime / $count, 1) . ' days' : 'N/A';
                            @endphp
                        </strong></p>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    Top Performers
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @php
                                            $topPerformers = \App\Models\User::where('role_id', 1)
                                                ->withCount(['assignedJobs' => function($query) {
                                                    $query->where('status', 'final');
                                                }])
                                                ->orderBy('assigned_jobs_count', 'desc')
                                                ->take(5)
                                                ->get();
                                        @endphp

                                        @foreach($topPerformers as $performer)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $performer->name }}
                                                <span class="badge bg-primary rounded-pill">{{ $performer->assigned_jobs_count }} completed</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    Tasks By Status
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @php
                                            $statusCounts = [
                                                'assigned' => \App\Models\EmployeeJobDesk::where('status', 'assigned')->count(),
                                                'completed' => \App\Models\EmployeeJobDesk::where('status', 'completed')->count(),
                                                'in_review_kadiv' => \App\Models\EmployeeJobDesk::where('status', 'in_review_kadiv')->count(),
                                                'in_review_director' => \App\Models\EmployeeJobDesk::where('status', 'in_review_director')->count(),
                                                'rejected' => \App\Models\EmployeeJobDesk::whereIn('status', ['rejected_kadiv', 'rejected_director'])->count(),
                                                'final' => \App\Models\EmployeeJobDesk::where('status', 'final')->count(),
                                            ];
                                        @endphp

                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Assigned
                                            <span class="badge bg-secondary rounded-pill">{{ $statusCounts['assigned'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Completed (Waiting Review)
                                            <span class="badge bg-info rounded-pill">{{ $statusCounts['completed'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            In Review (Kadiv/Director)
                                            <span class="badge bg-warning rounded-pill">{{ $statusCounts['in_review_kadiv'] + $statusCounts['in_review_director'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Rejected (Needs Revision)
                                            <span class="badge bg-danger rounded-pill">{{ $statusCounts['rejected'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Final (Completed)
                                            <span class="badge bg-success rounded-pill">{{ $statusCounts['final'] }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
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
        $('#dataTable').DataTable();
    });
</script>
@endpush