<!-- resources/views/admin/users/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengguna: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Pengguna
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Pengguna Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Peran</th>
                            <td>
                                <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($user->role->name) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Divisi</th>
                            <td>{{ $user->division->name ?? 'Tidak Ada' }}</td>
                        </tr>
                        {{-- <tr>
                            <th>Bergabung Pada</th>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Pengguna Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Aktivitas Pengguna</h6>
        </div>
        <div class="card-body">
            @if($user->isEmployee())
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tugas Tertunda</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-danger h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tugas Ditolak</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <h5>Tugas Terbaru</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tugas</th>
                                <th>Status</th>
                                <th>Selesai Pada</th>
                                <th>Penilaian Kadiv</th>
                                <th>Penilaian Direktur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->assignedJobs()->with('jobDesk')->latest()->take(5)->get() as $task)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $task->jobDesk->title }}</td>
                                <td>{!! $task->status_badge !!}</td>
                                <td>{{ $task->completed_at ? $task->completed_at->format('d M Y') : 'Belum Selesai' }}</td>
                                <td>{{ $task->kadiv_rating ?? 'Belum Dinilai' }}</td>
                                <td>{{ $task->director_rating ?? 'Belum Dinilai' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($user->isDivisionHead())
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Job Desk Dibuat</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJobDesks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Diperiksa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reviewedTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Review</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReviews }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <h5>Job Desk Terbaru</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job Desk</th>
                                <th>Dibuat Pada</th>
                                <th>Tenggat Waktu</th>
                                <th>Ditugaskan Kepada</th>
                                <th>Status</th>
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
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Diperiksa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reviewedTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Review</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReviews }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tugas Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Penilaian</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgRating ? number_format($avgRating, 1) : 'N/A' }}/4</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <h5>Review Terbaru</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tugas</th>
                                <th>Karyawan</th>
                                <th>Divisi</th>
                                <th>Penilaian</th>
                                <th>Diperiksa Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\EmployeeJobDesk::whereNotNull('director_reviewed_at')->with(['employee', 'jobDesk', 'employee.division'])->latest('director_reviewed_at')->take(5)->get() as $review)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $review->jobDesk->title }}</td>
                                <td>{{ $review->employee->name }}</td>
                                <td>{{ $review->employee->division->name ?? 'N/A' }}</td>
                                <td>{{ $review->director_rating }}/4</td>
                                <td>{{ $review->director_reviewed_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <h4 class="alert-heading">Admin</h4>
                    <p>Pengguna ini memiliki hak akses administrator. Administrator memiliki akses penuh untuk mengelola pengguna, divisi, peran, dan melihat semua data sistem.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection