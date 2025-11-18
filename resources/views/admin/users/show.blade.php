<!-- resources/views/admin/users/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Detail Pengguna: {{ $user->name }}</h1>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-sm-0">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm btn-md-md">
                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm btn-md-md">
                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Info Pengguna Card -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="35%" class="ps-0">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0">Email</th>
                            <td class="text-break">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0">Peran</th>
                            <td>
                                <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($user->role->name) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-0">Divisi</th>
                            <td>{{ $user->division->name ?? 'Tidak Ada' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Pengguna Card -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Aktivitas Pengguna</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            @if($user->isEmployee())
                <!-- Statistik Karyawan -->
                <div class="row g-2 g-md-3 mb-3 mb-md-4">
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tugas Tertunda</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-danger h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tugas Ditolak</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Tugas Terbaru</h5>

                <!-- Tampilan Mobile (Card) -->
                <div class="d-block d-lg-none">
                    @foreach($user->assignedJobs()->with('jobDesk')->latest()->take(5)->get() as $task)
                    <div class="card mb-2 border-start border-3 border-{{ $task->status === 'final' ? 'success' : ($task->status === 'assigned' ? 'secondary' : 'warning') }}">
                        <div class="card-body p-2">
                            <h6 class="mb-1 font-weight-bold">{{ $task->jobDesk->title }}</h6>
                            <div class="mb-2">{!! $task->status_badge !!}</div>
                            <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i> {{ $task->completed_at ? $task->completed_at->format('d M Y') : 'Belum Selesai' }}</small>
                            <small class="text-muted d-block"><i class="fas fa-star me-1"></i> Kadiv: {{ $task->kadiv_rating ?? 'Belum Dinilai' }} | Direktur: {{ $task->director_rating ?? 'Belum Dinilai' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tampilan Desktop (Table) -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 30%;">Tugas</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 15%;">Selesai Pada</th>
                                <th style="width: 15%;">Penilaian Kadiv</th>
                                <th style="width: 20%;">Penilaian Direktur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->assignedJobs()->with('jobDesk')->latest()->take(5)->get() as $task)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $task->jobDesk->title }}</td>
                                <td>{!! $task->status_badge !!}</td>
                                <td>{{ $task->completed_at ? $task->completed_at->format('d M Y') : 'Belum Selesai' }}</td>
                                <td class="text-center">{{ $task->kadiv_rating ?? 'Belum Dinilai' }}</td>
                                <td class="text-center">{{ $task->director_rating ?? 'Belum Dinilai' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($user->isDivisionHead())
                <!-- Statistik Kepala Divisi -->
                <div class="row g-2 g-md-3 mb-3 mb-md-4">
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Job Desk Dibuat</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJobDesks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Diperiksa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reviewedTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Review</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReviews }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Job Desk Terbaru</h5>

                <!-- Tampilan Mobile (Card) -->
                <div class="d-block d-lg-none">
                    @foreach(\App\Models\JobDesk::where('created_by', $user->id)->with('assignments.employee')->latest()->take(5)->get() as $jobDesk)
                    <div class="card mb-2 border-start border-3 border-primary">
                        <div class="card-body p-2">
                            <h6 class="mb-1 font-weight-bold">{{ $jobDesk->title }}</h6>
                            <small class="text-muted d-block"><i class="fas fa-calendar-plus me-1"></i> Dibuat: {{ $jobDesk->created_at->format('d M Y') }}</small>
                            <small class="text-muted d-block"><i class="fas fa-calendar-check me-1"></i> Tenggat: {{ $jobDesk->deadline->format('d M Y') }}</small>
                            <small class="text-muted d-block"><i class="fas fa-users me-1"></i> {{ $jobDesk->assignments->count() }} karyawan</small>
                            <div class="mt-2">
                                @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                    <span class="badge bg-primary">Sedang Berjalan</span>
                                @else
                                    <span class="badge bg-warning">Belum Dimulai</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tampilan Desktop (Table) -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Job Desk</th>
                                <th style="width: 15%;">Dibuat Pada</th>
                                <th style="width: 15%;">Tenggat Waktu</th>
                                <th style="width: 20%;">Ditugaskan Kepada</th>
                                <th style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\JobDesk::where('created_by', $user->id)->with('assignments.employee')->latest()->take(5)->get() as $jobDesk)
                            <tr>
                                <td>{{ $jobDesk->title }}</td>
                                <td>{{ $jobDesk->created_at->format('d M Y') }}</td>
                                <td>{{ $jobDesk->deadline->format('d M Y') }}</td>
                                <td>{{ $jobDesk->assignments->count() }} karyawan</td>
                                <td>
                                    @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                        <span class="badge bg-primary">Sedang Berjalan</span>
                                    @else
                                        <span class="badge bg-warning">Belum Dimulai</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($user->isDirector())
                <!-- Statistik Direktur -->
                <div class="row g-2 g-md-3 mb-3 mb-md-4">
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Diperiksa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reviewedTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Review</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReviews }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tugas Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body p-2 p-md-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto d-none d-sm-block">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Review Terbaru</h5>

                <!-- Tampilan Mobile (Card) -->
                <div class="d-block d-lg-none">
                    @foreach(\App\Models\EmployeeJobDesk::whereNotNull('director_reviewed_at')->with(['employee', 'jobDesk', 'employee.division'])->latest('director_reviewed_at')->take(5)->get() as $review)
                    <div class="card mb-2 border-start border-3 border-info">
                        <div class="card-body p-2">
                            <h6 class="mb-1 font-weight-bold">{{ $review->jobDesk->title }}</h6>
                            <small class="text-muted d-block"><i class="fas fa-user me-1"></i> {{ $review->employee->name }}</small>
                            <small class="text-muted d-block"><i class="fas fa-building me-1"></i> {{ $review->employee->division->name ?? 'N/A' }}</small>
                            <small class="text-muted d-block"><i class="fas fa-star me-1"></i> Penilaian: {{ $review->director_rating }}/4</small>
                            <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i> {{ $review->director_reviewed_at->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tampilan Desktop (Table) -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 30%;">Tugas</th>
                                <th style="width: 20%;">Karyawan</th>
                                <th style="width: 15%;">Divisi</th>
                                <th style="width: 15%;">Penilaian</th>
                                <th style="width: 15%;">Diperiksa Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\EmployeeJobDesk::whereNotNull('director_reviewed_at')->with(['employee', 'jobDesk', 'employee.division'])->latest('director_reviewed_at')->take(5)->get() as $review)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $review->jobDesk->title }}</td>
                                <td>{{ $review->employee->name }}</td>
                                <td>{{ $review->employee->division->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $review->director_rating }}/4</td>
                                <td>{{ $review->director_reviewed_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="alert alert-info">
                    <h4 class="alert-heading h5 h-md-4">Admin</h4>
                    <p class="mb-0">Pengguna ini memiliki hak akses administrator. Administrator memiliki akses penuh untuk mengelola pengguna, divisi, peran, dan melihat semua data sistem.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection