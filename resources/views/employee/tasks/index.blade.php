@extends('layouts.employee')

@section('title', 'My Tasks')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">My Tasks</h2>
        <p class="text-secondary mb-0">Manage and track all your assigned tasks</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light dropdown-toggle">
            <i class="fas fa-filter me-2"></i>Filter Tasks
        </button>
        <button class="btn btn-primary">
            <i class="fas fa-sort me-2"></i>Sort
        </button>
    </div>
</div>

<!-- Tasks Card -->
<div class="card h-100 mb-4">
    <div class="card-header">
        <h5 class="card-header-title">
            <i class="fas fa-tasks me-2 text-primary"></i>
            All Tasks
        </h5>
        <div class="card-header-actions">
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-clipboard-list me-1"></i>
                {{$assignments->count()}} Total Tasks
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Task Tabs -->
        <ul class="nav nav-tabs px-4 pt-3" id="taskTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                    <i class="fas fa-hourglass-half me-2"></i>Pending
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inprogress-tab" data-bs-toggle="tab" data-bs-target="#inprogress" type="button" role="tab" aria-controls="inprogress" aria-selected="false">
                    <i class="fas fa-spinner me-2"></i>In Progress
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                    <i class="fas fa-times-circle me-2"></i>Rejected
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                    <i class="fas fa-check-circle me-2"></i>Completed
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4" id="taskTabsContent">
            <!-- Pending Tasks Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $pendingFound = false; @endphp
                            @foreach($assignments as $assignment)
                                @if($assignment->status == 'assigned')
                                    @php $pendingFound = true; @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $assignment->jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($assignment->jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ \Carbon\Carbon::parse($assignment->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $assignment->jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>{!! $assignment->status_badge !!}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('employee.tasks.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$pendingFound)
                                <tr>
                                    <td colspan="5">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">No pending tasks found. All caught up!</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- In Progress Tasks Tab -->
            <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $inProgressFound = false; @endphp
                            @foreach($assignments as $assignment)
                                @if(in_array($assignment->status, ['completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director']))
                                    @php $inProgressFound = true; @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $assignment->jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($assignment->jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ \Carbon\Carbon::parse($assignment->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $assignment->jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>{!! $assignment->status_badge !!}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('employee.tasks.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$inProgressFound)
                                <tr>
                                    <td colspan="5">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">No tasks in progress at the moment.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rejected Tasks Tab -->
            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Feedback</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rejectedFound = false; @endphp
                            @foreach($assignments as $assignment)
                                @if(in_array($assignment->status, ['rejected_kadiv', 'rejected_director']))
                                    @php $rejectedFound = true; @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $assignment->jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($assignment->jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ \Carbon\Carbon::parse($assignment->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $assignment->jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>{!! $assignment->status_badge !!}</td>
                                        <td>
                                            <button class="btn btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $assignment->status == 'rejected_kadiv' ? ($assignment->kadiv_notes ?? 'No feedback provided.') : ($assignment->director_notes ?? 'No feedback provided.') }}">
                                                <i class="fas fa-comment-dots me-1"></i> View Feedback
                                            </button>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('employee.tasks.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Revise
                                                </a>
                                                <button type="button" class="btn btn-sm btn-light">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$rejectedFound)
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-success d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-check-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Great job! You don't have any rejected tasks.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Tasks Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Kadiv Rating</th>
                                <th>Director Rating</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $completedFound = false; @endphp
                            @foreach($assignments as $assignment)
                                @if($assignment->status == 'final')
                                    @php $completedFound = true; @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $assignment->jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($assignment->jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-success-light text-success">
                                                <i class="far fa-calendar-check me-1"></i>
                                                {{ $assignment->jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="fw-semibold">{{ $assignment->kadiv_rating }}</span>/4
                                                </div>
                                                <div class="progress flex-grow-1" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($assignment->kadiv_rating/4)*100 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="fw-semibold">{{ $assignment->director_rating }}</span>/4
                                                </div>
                                                <div class="progress flex-grow-1" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($assignment->director_rating/4)*100 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('employee.tasks.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$completedFound)
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">You don't have any completed tasks yet. Keep working!</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection