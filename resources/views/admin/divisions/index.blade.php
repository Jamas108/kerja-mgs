@extends('layouts.admin')

@section('title', 'Divisions Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Divisions Management</h1>
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Division
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Divisions</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Employees</th>
                            <th>Division Head</th>
                            <th>Total Tasks</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $division)
                        <tr>
                            <td>{{ $division->name }}</td>
                            <td>
                                {{ $division->users_count }}
                            </td>
                            <td>
                                @php
                                    $divisionHead = $division->users()->where('role_id', 4)->first();
                                @endphp
                                {{ $divisionHead ? $divisionHead->name : 'Not Assigned' }}
                            </td>
                            <td>
                                {{ $division->jobDesks()->count() }}
                            </td>
                            <td>{{ $division->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this division?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Divisions Overview</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($divisions as $division)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">{{ $division->name }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-0">Employees</h5>
                                    <p class="mb-0 text-muted">{{ $division->users_count }}</p>
                                </div>
                                <div>
                                    <h5 class="mb-0">Tasks</h5>
                                    <p class="mb-0 text-muted">{{ $division->jobDesks()->count() }}</p>
                                </div>
                                <div>
                                    <h5 class="mb-0">Head</h5>
                                    @php
                                        $divisionHead = $division->users()->where('role_id', 4)->first();
                                    @endphp
                                    <p class="mb-0 text-muted">{{ $divisionHead ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                            <div class="progress">
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
                                    {{ number_format($completionPercentage, 0) }}% Completed
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> View Details
                            </a>
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
        $('#dataTable').DataTable();
    });
</script>
@endpush
