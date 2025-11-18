@extends('layouts.admin')

@section('title', 'Employee Performance Management')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .page-header {
            text-align: center;
        }

        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .btn-group-mobile {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-group-mobile .btn {
            width: 100%;
            justify-content: center;
        }

        .card-header-title {
            font-size: 1rem;
        }

        .badge {
            font-size: 0.75rem;
        }
    }

    @media (min-width: 768px) {
        .btn-group-mobile {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
        }
    }

    /* Progress bar styling */
    .progress {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .progress-bar {
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.5rem 0.75rem;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    @media (max-width: 575.98px) {
        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }

    /* Card responsive */
    .card-header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .card-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    /* DataTables responsive styling */
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
    }

    @media (max-width: 575.98px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            text-align: center;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
            margin-top: 1rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="flex-grow-1">
            <h2 class="h3 mb-2 text-gray-800 fw-bold">Employee Performance Management</h2>
            <p class="text-secondary mb-0">Monitor and evaluate employee performance across all departments</p>
        </div>
        <div class="btn-group-mobile">
            <a href="{{ route('admin.performances.compare') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>Compare Performance
            </a>
            <a href="{{ route('admin.performances.report') }}" class="btn btn-primary">
                <i class="fas fa-file-alt me-2"></i>Performance Report
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter & Search</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.performances.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="division_id" class="form-label">Division</label>
                    <select name="division_id" id="division_id" class="form-control">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="performance_category" class="form-label">Performance Category</label>
                    <select name="performance_category" id="performance_category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($performanceCategories as $key => $category)
                            <option value="{{ $key }}" {{ request('performance_category') == $key ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Search Employee</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="Search by name or email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.performances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employee Performance Card -->
<div class="card shadow h-100 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-header-title mb-2 mb-md-0">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Employee Performance
        </h5>
        <div class="card-header-actions">
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-users me-1"></i>
                {{ $employees->count() }} Total Employees
            </span>
        </div>
    </div>

    <div class="card-body">
        <!-- Desktop View -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover" id="employee-performance-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th>Email</th>
                        <th>Total Tasks</th>
                        <th>Completed Tasks</th>
                        <th>Performance Score</th>
                        <th>Category</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $employee->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $employee->division->name ?? 'No Division' }}</span>
                            </td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-tasks me-1"></i>
                                    {{ $employee->total_assignments }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $employee->completed_assignments }}
                                </span>
                            </td>
                            <td>
                                @if ($employee->performance_score)
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $percentage = ($employee->performance_score / 4) * 100;
                                            $colorClass = 'bg-danger';

                                            if ($employee->performance_score >= 3.7) {
                                                $colorClass = 'bg-success';
                                            } elseif ($employee->performance_score >= 3) {
                                                $colorClass = 'bg-info';
                                            } elseif ($employee->performance_score >= 2.5) {
                                                $colorClass = 'bg-primary';
                                            } elseif ($employee->performance_score >= 2) {
                                                $colorClass = 'bg-warning';
                                            }
                                        @endphp

                                        <div class="progress-bar {{ $colorClass }}" role="progressbar"
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $employee->performance_score }}"
                                            aria-valuemin="0" aria-valuemax="4">
                                            {{ $employee->performance_score }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No rating yet</span>
                                @endif
                            </td>
                            <td>
                                @if ($employee->performance_score)
                                    @php
                                        $badgeClass = 'bg-danger';

                                        if ($employee->performance_score >= 3.7) {
                                            $badgeClass = 'bg-success';
                                        } elseif ($employee->performance_score >= 3) {
                                            $badgeClass = 'bg-info';
                                        } elseif ($employee->performance_score >= 2.5) {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($employee->performance_score >= 2) {
                                            $badgeClass = 'bg-warning';
                                        }
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $employee->performance_category }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not Rated</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.performances.show', $employee->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>

                                    @if ($employee->performance_score >= 3 && !$employee->has_pending_promotion)
                                        <a href="{{ route('admin.performances.propose_promotion', $employee->id) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-award me-1"></i> Propose Promotion
                                        </a>
                                    @elseif ($employee->has_pending_promotion)
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-clock me-1"></i> Promotion Pending
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">No employee data available.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile/Tablet View -->
        <div class="d-lg-none">
            @forelse ($employees as $employee)
                <div class="card mb-3 border-left-primary shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="font-weight-bold text-primary mb-1">{{ $employee->name }}</h6>
                                <small class="text-muted d-block">{{ $employee->email }}</small>
                                <span class="badge bg-info mt-1">{{ $employee->division->name ?? 'No Division' }}</span>
                            </div>
                            <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Total Tasks</small>
                                <span class="badge bg-secondary w-100">
                                    <i class="fas fa-tasks me-1"></i>
                                    {{ $employee->total_assignments }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Completed Tasks</small>
                                <span class="badge bg-success w-100">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $employee->completed_assignments }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Performance Score</small>
                            @if ($employee->performance_score)
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $percentage = ($employee->performance_score / 4) * 100;
                                        $colorClass = 'bg-danger';

                                        if ($employee->performance_score >= 3.7) {
                                            $colorClass = 'bg-success';
                                        } elseif ($employee->performance_score >= 3) {
                                            $colorClass = 'bg-info';
                                        } elseif ($employee->performance_score >= 2.5) {
                                            $colorClass = 'bg-primary';
                                        } elseif ($employee->performance_score >= 2) {
                                            $colorClass = 'bg-warning';
                                        }
                                    @endphp

                                    <div class="progress-bar {{ $colorClass }}" role="progressbar"
                                        style="width: {{ $percentage }}%"
                                        aria-valuenow="{{ $employee->performance_score }}"
                                        aria-valuemin="0" aria-valuemax="4">
                                        {{ $employee->performance_score }}
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">No rating yet</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Category</small>
                            @if ($employee->performance_score)
                                @php
                                    $badgeClass = 'bg-danger';

                                    if ($employee->performance_score >= 3.7) {
                                        $badgeClass = 'bg-success';
                                    } elseif ($employee->performance_score >= 3) {
                                        $badgeClass = 'bg-info';
                                    } elseif ($employee->performance_score >= 2.5) {
                                        $badgeClass = 'bg-primary';
                                    } elseif ($employee->performance_score >= 2) {
                                        $badgeClass = 'bg-warning';
                                    }
                                @endphp

                                <span class="badge {{ $badgeClass }} w-100 py-2">
                                    {{ $employee->performance_category }}
                                </span>
                            @else
                                <span class="badge bg-secondary w-100 py-2">Not Rated</span>
                            @endif
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('admin.performances.show', $employee->id) }}"
                                class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>

                            @if ($employee->performance_score >= 3 && !$employee->has_pending_promotion)
                                <a href="{{ route('admin.performances.propose_promotion', $employee->id) }}"
                                    class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-award me-1"></i> Propose Promotion
                                </a>
                            @elseif ($employee->has_pending_promotion)
                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                    <i class="fas fa-clock me-1"></i> Promotion Pending
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0">No employee data available.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Only initialize DataTable on desktop view
        if ($(window).width() >= 992) {
            $('#employee-performance-table').DataTable(
            //     {
            //     "language": {
            //         "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
            //     },
            //     "responsive": true,
            //     "pageLength": 10,
            //     "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            //     "search": {
            //         "smart": true,
            //         "caseInsensitive": true
            //     },
            //     "ordering": true,
            //     "info": true,
            //     "autoWidth": false,
            //     "columnDefs": [
            //         { "orderable": false, "targets": "no-sort" }
            //     ],
            //     "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            //     "initComplete": function() {
            //         // Customize search input
            //         $('.dataTables_filter input').attr('placeholder', 'Search employees...');
            //         $('.dataTables_filter input').addClass('form-control');
            //         $('.dataTables_filter label').addClass('mb-0');

            //         // Customize length menu
            //         $('.dataTables_length select').addClass('form-select form-select-sm');
            //     }
            // }
        );
        }
    });
</script>
@endpush
@endsection