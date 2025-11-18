@extends('layouts.admin')

@section('title', 'Kelola Divisi')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Divisions Management</h1>
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary btn-sm btn-md-md">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New Division</span><span class="d-inline d-sm-none">Add</span>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">All Divisions</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Tampilan Mobile (Card) -->
            <div class="d-block d-lg-none">
                @foreach($divisions as $division)
                <div class="card mb-3 border-start border-4 border-primary">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold">{{ $division->name }}</h6>
                                @php
                                    $divisionHead = $division->users()->where('role_id', 4)->first();
                                @endphp
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i>
                                    {{ $divisionHead ? $divisionHead->name : 'Not Assigned' }}
                                </small>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4">
                                <small class="text-muted d-block">Employees</small>
                                <span class="font-weight-bold">{{ $division->users_count }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Tasks</small>
                                <span class="font-weight-bold">{{ $division->jobDesks()->count() }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Created</small>
                                <span class="font-weight-bold">{{ $division->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        @php
                            $totalAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                $query->where('division_id', $division->id);
                            })->count();

                            $completedAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                $query->where('division_id', $division->id);
                            })->where('status', 'final')->count();

                            $completionPercentage = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;
                        @endphp

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Completion Progress</small>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%"
                                    aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($completionPercentage, 0) }}%
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">View</span>
                            </a>
                            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
                            </a>
                            <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="d-inline flex-fill flex-sm-grow-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this division?')">
                                    <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tampilan Desktop (Table) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Name</th>
                            <th style="width: 10%;">Employees</th>
                            <th style="width: 20%;">Division Head</th>
                            <th style="width: 10%;">Total Tasks</th>
                            <th style="width: 15%;">Created At</th>
                            <th style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $division)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $division->name }}</td>
                            <td class="text-center">{{ $division->users_count }}</td>
                            <td>
                                @php
                                    $divisionHead = $division->users()->where('role_id', 4)->first();
                                @endphp
                                {{ $divisionHead ? $divisionHead->name : 'Not Assigned' }}
                            </td>
                            <td class="text-center">{{ $division->jobDesks()->count() }}</td>
                            <td>{{ $division->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this division?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($divisions->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted">No divisions registered yet.</p>
                <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Division
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Overview Card -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Divisions Overview</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row">
                @foreach($divisions as $division)
                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="m-0 font-weight-bold text-truncate">{{ $division->name }}</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-center flex-fill">
                                    <h5 class="mb-1 small">Employees</h5>
                                    <p class="mb-0 h5 text-primary">{{ $division->users_count }}</p>
                                </div>
                                <div class="text-center flex-fill border-start border-end">
                                    <h5 class="mb-1 small">Tasks</h5>
                                    <p class="mb-0 h5 text-primary">{{ $division->jobDesks()->count() }}</p>
                                </div>
                                <div class="text-center flex-fill">
                                    <h5 class="mb-1 small">Head</h5>
                                    @php
                                        $divisionHead = $division->users()->where('role_id', 4)->first();
                                    @endphp
                                    <p class="mb-0 h5 text-primary">{{ $divisionHead ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>

                            <small class="text-muted d-block mb-1">Completion Progress</small>
                            <div class="progress" style="height: 24px;">
                                @php
                                    $totalAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                        $query->where('division_id', $division->id);
                                    })->count();

                                    $completedAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                        $query->where('division_id', $division->id);
                                    })->where('status', 'final')->count();

                                    $completionPercentage = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%"
                                    aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($completionPercentage, 0) }}%
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-2">
                            <div class="btn-group d-flex" role="group">
                                <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-eye"></i> <span class="d-none d-md-inline">View</span>
                                </a>
                                <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm flex-fill">
                                    <i class="fas fa-edit"></i> <span class="d-none d-md-inline">Edit</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable hanya untuk tampilan desktop
        if ($(window).width() >= 992) {
            $('#dataTable').DataTable();
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush