<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container-fluid px-2 px-md-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
            <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Kartu Statistik -->
        <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
            <div class="col-6 col-lg-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body p-2 p-md-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengguna</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users }}</div>
                            </div>
                            <div class="col-auto d-none d-sm-block">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body p-2 p-md-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Karyawan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees }}</div>
                            </div>
                            <div class="col-auto d-none d-sm-block">
                                <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body p-2 p-md-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Tugas Selesai</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $finalTasks->count() }}</div>
                            </div>
                            <div class="col-auto d-none d-sm-block">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body p-2 p-md-3">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Tugas Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                            </div>
                            <div class="col-auto d-none d-sm-block">
                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Tugas Selesai -->
        <div class="row mb-3 mb-md-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-2 py-md-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tugas yang Telah Diselesaikan</h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Judul Tugas</th>
                                        <th>Karyawan</th>
                                        <th class="d-none d-md-table-cell">Divisi</th>
                                        <th>Rating Kadiv</th>
                                        <th class="d-none d-lg-table-cell">Rating Direktur</th>
                                        <th class="d-none d-lg-table-cell">Selesai Pada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($finalTasks as $task)
                                        <tr>
                                            <td>{{ $task->jobDesk->title }}</td>
                                            <td>{{ $task->employee->name }}</td>
                                            <td class="d-none d-md-table-cell">
                                                {{ $task->employee->division->name ?? 'N/A' }}</td>
                                            <td>{{ $task->kadiv_rating }} / 4</td>
                                            <td class="d-none d-lg-table-cell">{{ $task->director_rating }} / 4</td>
                                            <td class="d-none d-lg-table-cell">
                                                {{ $task->completed_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Divisi & Aktivitas Terkini -->
        <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                <div class="card shadow h-100">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Divisi</h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Divisi</th>
                                        <th>Karyawan</th>
                                        <th class="d-none d-sm-table-cell">Tugas</th>
                                        <th>Selesai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (\App\Models\Division::all() as $division)
                                        <tr>
                                            <td>{{ $division->name }}</td>
                                            <td>{{ $division->users()->where('role_id', 1)->count() }}</td>
                                            <td class="d-none d-sm-table-cell">{{ $division->jobDesks()->count() }}</td>
                                            <td>
                                                {{ \App\Models\EmployeeJobDesk::whereHas('jobDesk', function ($query) use ($division) {
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

            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terkini</h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tugas</th>
                                        <th class="d-none d-md-table-cell">Karyawan</th>
                                        <th>Status</th>
                                        <th class="d-none d-sm-table-cell">Diperbarui</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (\App\Models\EmployeeJobDesk::with(['employee', 'jobDesk'])->orderBy('updated_at', 'desc')->take(5)->get() as $activity)
                                        <tr>
                                            <td>{{ $activity->jobDesk->title }}</td>
                                            <td class="d-none d-md-table-cell">{{ $activity->employee->name }}</td>
                                            <td>{!! $activity->status_badge !!}</td>
                                            <td class="d-none d-sm-table-cell">{{ $activity->updated_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Performa -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-3 mb-md-4">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ikhtisar Kinerja</h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="alert alert-info mb-3 mb-md-4">
                            <h4 class="alert-heading h5 h-md-4">Ringkasan Kinerja Sistem</h4>
                            <p class="mb-1">Total tugas selesai: <strong>{{ $finalTasks->count() }}</strong></p>
                            <p class="mb-1">Rata-rata rating Kadiv:
                                <strong>{{ $finalTasks->avg('kadiv_rating') ? number_format($finalTasks->avg('kadiv_rating'), 2) : 'N/A' }}/4</strong>
                            </p>
                            <p class="mb-1">Rata-rata rating Direktur:
                                <strong>{{ $finalTasks->avg('director_rating') ? number_format($finalTasks->avg('director_rating'), 2) : 'N/A' }}/4</strong>
                            </p>
                            <p class="mb-0">Rata-rata waktu penyelesaian: <strong>
                                    @php
                                        $avgTime = 0;
                                        $count = 0;
                                        foreach ($finalTasks as $task) {
                                            if ($task->completed_at && $task->jobDesk && $task->jobDesk->created_at) {
                                                $diff = $task->completed_at->diffInDays($task->jobDesk->created_at);
                                                $avgTime += $diff;
                                                $count++;
                                            }
                                        }
                                        echo $count > 0 ? number_format($avgTime / $count, 1) . ' hari' : 'N/A';
                                    @endphp
                                </strong></p>
                        </div>
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <strong>Karyawan Terbaik</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <ul class="list-group list-group-flush">
                                            @php
                                                $topPerformers = \App\Models\User::where('role_id', 1)
                                                    ->withCount([
                                                        'assignedJobs' => function ($query) {
                                                            $query->where('status', 'final');
                                                        },
                                                    ])
                                                    ->orderBy('assigned_jobs_count', 'desc')
                                                    ->take(5)
                                                    ->get();
                                            @endphp

                                            @foreach ($topPerformers as $performer)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                    <span class="text-truncate me-2">{{ $performer->name }}</span>
                                                    <span
                                                        class="badge bg-primary rounded-pill">{{ $performer->assigned_jobs_count }}
                                                        selesai</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <strong>Tugas Berdasarkan Status</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <ul class="list-group list-group-flush">
                                            @php
                                                $statusCounts = [
                                                    'assigned' => \App\Models\EmployeeJobDesk::where(
                                                        'status',
                                                        'assigned',
                                                    )->count(),
                                                    'completed' => \App\Models\EmployeeJobDesk::where(
                                                        'status',
                                                        'completed',
                                                    )->count(),
                                                    'in_review_kadiv' => \App\Models\EmployeeJobDesk::where(
                                                        'status',
                                                        'in_review_kadiv',
                                                    )->count(),
                                                    'in_review_director' => \App\Models\EmployeeJobDesk::where(
                                                        'status',
                                                        'in_review_director',
                                                    )->count(),
                                                    'rejected' => \App\Models\EmployeeJobDesk::whereIn('status', [
                                                        'rejected_kadiv',
                                                        'rejected_director',
                                                    ])->count(),
                                                    'final' => \App\Models\EmployeeJobDesk::where(
                                                        'status',
                                                        'final',
                                                    )->count(),
                                                ];
                                            @endphp

                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                <span class="text-truncate me-2">Ditugaskan</span>
                                                <span
                                                    class="badge bg-secondary rounded-pill">{{ $statusCounts['assigned'] }}</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                <span class="text-truncate me-2">Selesai (Menunggu Review)</span>
                                                <span
                                                    class="badge bg-info rounded-pill">{{ $statusCounts['completed'] }}</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                <span class="text-truncate me-2">Direview (Kadiv/Direktur)</span>
                                                <span
                                                    class="badge bg-warning rounded-pill">{{ $statusCounts['in_review_kadiv'] + $statusCounts['in_review_director'] }}</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                <span class="text-truncate me-2">Ditolak (Perlu Revisi)</span>
                                                <span
                                                    class="badge bg-danger rounded-pill">{{ $statusCounts['rejected'] }}</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 px-md-3">
                                                <span class="text-truncate me-2">Final (Selesai)</span>
                                                <span
                                                    class="badge bg-success rounded-pill">{{ $statusCounts['final'] }}</span>
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
