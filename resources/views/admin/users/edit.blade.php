@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank to keep current password</small>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="division_id" class="form-label">Division</label>
                        <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id">
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Required for Employees and Division Heads</small>
                        @error('division_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Activity Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Activity</h6>
        </div>
        <div class="card-body">
            @if($user->isEmployee())
                @php
                    $completedTasks = $user->assignedJobs()->where('status', 'final')->count();
                    $pendingTasks = $user->assignedJobs()->whereNotIn('status', ['final'])->count();
                    $rejectedTasks = $user->assignedJobs()->whereIn('status', ['rejected_kadiv', 'rejected_director'])->count();
                    $avgRating = $user->assignedJobs()->where('status', 'final')->avg('kadiv_rating');
                    $directorAvgRating = $user->assignedJobs()->where('status', 'final')->avg('director_rating');
                @endphp
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pending Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Rating</div>
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
                    <h5>Recent Tasks</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Completed</th>
                                <th>Kadiv Rating</th>
                                <th>Director Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->assignedJobs()->with('jobDesk')->latest()->take(5)->get() as $task)
                            <tr>
                                <td>{{ $task->jobDesk->title }}</td>
                                <td>{!! $task->status_badge !!}</td>
                                <td>{{ $task->completed_at ? $task->completed_at->format('d M Y') : 'Not Completed' }}</td>
                                <td>{{ $task->kadiv_rating ?? 'Not Rated' }}</td>
                                <td>{{ $task->director_rating ?? 'Not Rated' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($user->isDivisionHead())
                @php
                    $totalJobDesks = \App\Models\JobDesk::where('created_by', $user->id)->count();
                    $reviewedTasks = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                        $query->where('division_id', $user->division_id);
                    })->whereNotNull('kadiv_rating')->count();
                    $pendingReviews = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                        $query->where('division_id', $user->division_id);
                    })->where('status', 'completed')->count();
                    $avgRating = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                        $query->where('division_id', $user->division_id);
                    })->whereNotNull('kadiv_rating')->avg('kadiv_rating');
                @endphp
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Created Job Desks</div>
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
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Reviewed Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Reviews</div>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Rating Given</div>
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
                    <h5>Recent Job Desks</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job Desk</th>
                                <th>Created</th>
                                <th>Deadline</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\JobDesk::where('created_by', $user->id)->with('assignments.employee')->latest()->take(5)->get() as $jobDesk)
                            <tr>
                                <td>{{ $jobDesk->title }}</td>
                                <td>{{ $jobDesk->created_at->format('d M Y') }}</td>
                                <td>{{ $jobDesk->deadline->format('d M Y') }}</td>
                                <td>{{ $jobDesk->assignments->count() }} employees</td>
                                <td>
                                    @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                        <span class="badge bg-primary">In Progress</span>
                                    @else
                                        <span class="badge bg-warning">Not Started</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($user->isDirector())
                @php
                    $reviewedTasks = \App\Models\EmployeeJobDesk::whereNotNull('director_rating')->count();
                    $pendingReviews = \App\Models\EmployeeJobDesk::where('status', 'in_review_director')->count();
                    $completedTasks = \App\Models\EmployeeJobDesk::where('status', 'final')->count();
                    $avgRating = \App\Models\EmployeeJobDesk::whereNotNull('director_rating')->avg('director_rating');
                @endphp
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Reviewed Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Reviews</div>
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
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Completed Tasks</div>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Rating Given</div>
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
                    <h5>Recent Reviews</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Employee</th>
                                <th>Division</th>
                                <th>Rating</th>
                                <th>Reviewed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\EmployeeJobDesk::whereNotNull('director_reviewed_at')->with(['employee', 'jobDesk', 'employee.division'])->latest('director_reviewed_at')->take(5)->get() as $review)
                            <tr>
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
                    <h4 class="alert-heading">Admin User</h4>
                    <p>This user has administrator privileges. Administrators have full access to manage users, divisions, roles, and view all system data.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle division field visibility based on role
        $('#role_id').change(function() {
            const selectedText = $(this).find('option:selected').text().toLowerCase();
            if (selectedText === 'karyawan' || selectedText === 'kepala divisi') {
                $('#division_id').prop('required', true);
                $('#division_id').closest('.col-md-6').show();
            } else {
                $('#division_id').prop('required', false);
                $('#division_id').closest('.col-md-6').hide();
            }
        });

        // Trigger change on page load
        $('#role_id').trigger('change');
    });
</script>
@endpush