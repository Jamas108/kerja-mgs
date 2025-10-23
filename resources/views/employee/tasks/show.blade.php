@extends('layouts.employee')

@section('title', 'Task Details')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Task Details</h2>
        <p class="text-secondary mb-0">{{ $assignment->jobDesk->title }}</p>
    </div>
    <div>
        <a href="{{ route('employee.tasks.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left me-2"></i> Back to Tasks
        </a>
    </div>
</div>

<!-- Main Content Row -->
@if(in_array($assignment->status, ['completed', 'in_review_director']))
<div class="row">
    <div class="col-lg-12">
        <!-- Task Details Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-clipboard-check me-2 text-primary"></i>
                    Task Details
                </h5>
                <div class="card-header-actions">
                    <span class="badge {{ \Carbon\Carbon::parse($assignment->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }} py-2 px-3">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ $assignment->jobDesk->deadline->format('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Title:</div>
                            <div class="col-md-9">{{ $assignment->jobDesk->title }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Description:</div>
                            <div class="col-md-9">{{ $assignment->jobDesk->description }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Status:</div>
                            <div class="col-md-9">{!! $assignment->status_badge !!}</div>
                        </div>
                    </div>
                    @if($assignment->completed_at)
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Completed:</div>
                            <div class="col-md-9">
                                <span class="badge bg-success-light text-success py-2 px-3">
                                    <i class="far fa-calendar-check me-1"></i>
                                    {{ $assignment->completed_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($assignment->evidence_file)
        <!-- Submission Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Your Submission
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Evidence Note:</div>
                            <div class="col-md-9">{{ $assignment->evidence_note }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Evidence File:</div>
                            <div class="col-md-9">
                                <a href="{{ asset('storage/' . $assignment->evidence_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-download me-1"></i> View/Download
                                </a>

                                @php
                                    $fileExtension = pathinfo(storage_path('app/public/' . $assignment->evidence_file), PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                                @endphp

                                @if($isImage)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $assignment->evidence_file) }}" class="img-fluid rounded" alt="Evidence Image" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@else
<div class="row">
    <div class="col-lg-8">
        <!-- Task Details Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-clipboard-check me-2 text-primary"></i>
                    Task Details
                </h5>
                <div class="card-header-actions">
                    <span class="badge {{ \Carbon\Carbon::parse($assignment->jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }} py-2 px-3">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ $assignment->jobDesk->deadline->format('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Title:</div>
                            <div class="col-md-9">{{ $assignment->jobDesk->title }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Description:</div>
                            <div class="col-md-9">{{ $assignment->jobDesk->description }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Status:</div>
                            <div class="col-md-9">{!! $assignment->status_badge !!}</div>
                        </div>
                    </div>
                    @if($assignment->completed_at)
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Completed:</div>
                            <div class="col-md-9">
                                <span class="badge bg-success-light text-success py-2 px-3">
                                    <i class="far fa-calendar-check me-1"></i>
                                    {{ $assignment->completed_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($assignment->evidence_file)
        <!-- Submission Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Your Submission
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Evidence Note:</div>
                            <div class="col-md-9">{{ $assignment->evidence_note }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Evidence File:</div>
                            <div class="col-md-9">
                                <a href="{{ asset('storage/' . $assignment->evidence_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-download me-1"></i> View/Download
                                </a>

                                @php
                                    $fileExtension = pathinfo(storage_path('app/public/' . $assignment->evidence_file), PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                                @endphp

                                @if($isImage)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $assignment->evidence_file) }}" class="img-fluid rounded" alt="Evidence Image" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($assignment->status == 'final')
        <!-- Kadiv Review Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-user-check me-2 text-primary"></i>
                    Kepala Divisi Review
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-3 fw-bold">Rating:</div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center">
                                    <div class="me-2"><span class="fw-semibold">{{ $assignment->kadiv_rating }}</span>/4</div>
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($assignment->kadiv_rating/4)*100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Notes:</div>
                            <div class="col-md-9">{{ $assignment->kadiv_notes ?: 'No notes provided.' }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Reviewed:</div>
                            <div class="col-md-9">
                                <span class="badge bg-info-light text-info py-1 px-2">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Director Review Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-user-tie me-2 text-primary"></i>
                    Director Review
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-3 fw-bold">Rating:</div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center">
                                    <div class="me-2"><span class="fw-semibold">{{ $assignment->director_rating }}</span>/4</div>
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($assignment->director_rating/4)*100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Notes:</div>
                            <div class="col-md-9">{{ $assignment->director_notes ?: 'No notes provided.' }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 fw-bold">Reviewed:</div>
                            <div class="col-md-9">
                                <span class="badge bg-info-light text-info py-1 px-2">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $assignment->director_reviewed_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if(in_array($assignment->status, ['assigned', 'rejected_kadiv', 'rejected_director']))
        <!-- Mark as Completed Card -->
        <div class="card mb-4" style="height: auto">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-check-circle me-2 text-primary"></i>
                    Mark as Completed
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee.tasks.complete', $assignment) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="evidence_note" class="form-label">Evidence Note</label>
                        <textarea class="form-control @error('evidence_note') is-invalid @enderror" id="evidence_note" name="evidence_note" rows="3" required>{{ old('evidence_note') }}</textarea>
                        @error('evidence_note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="evidence_file" class="form-label">Evidence File</label>
                        <input type="file" class="form-control @error('evidence_file') is-invalid @enderror" id="evidence_file" name="evidence_file" required>
                        @error('evidence_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i> Mark as Completed
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($assignment->status == 'in_review_kadiv')
        <!-- In Review by Kadiv Card -->
        <div class="card mb-4" style="height: auto">
            <div class="card-header bg-info-light">
                <h5 class="card-header-title text-info">
                    <i class="fas fa-user-check me-2"></i>
                    In Review by Kepala Divisi
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Your task is currently being reviewed by the Kepala Divisi</small>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="fas fa-spinner fa-spin text-info fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Under Review</h6>
                        <small class="text-muted">Submitted on {{ $assignment->completed_at->format('d M Y H:i') }}</small>
                    </div>
                </div>

                <div class="timeline small">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-success text-white">Completed</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-info text-white">Kepala Divisi Review</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-secondary text-white">Director Review</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($assignment->status == 'in_review_director')
        <!-- In Review by Director Card -->
        <div class="card mb-4" style="height: auto">
            <div class="card-header bg-primary-light">
                <h5 class="card-header-title text-primary">
                    <i class="fas fa-user-tie me-2"></i>
                    In Review by Director
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-primary py-2 d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Your task has been approved by Kadiv and is being reviewed by the Director</small>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="fas fa-spinner fa-spin text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Final Review</h6>
                        <small class="text-muted">Kadiv approved on {{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}</small>
                    </div>
                </div>

                <div class="timeline small">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-success text-white">Completed</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-success text-white">Kadiv Approved</span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="timeline-heading">
                                <span class="badge bg-primary text-white">Director Review</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($assignment->status == 'final')
        <!-- Final Rating Card -->
        <div class="card mb-4" style="height:auto">
            <div class="card-header bg-success-light">
                <h5 class="card-header-title text-success">
                    <i class="fas fa-award me-2"></i>
                    Final Rating
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="text-center mb-3">
                    <h1 class="display-4 fw-bold mb-0">{{ number_format(($assignment->kadiv_rating + $assignment->director_rating) / 2, 1) }}</h1>
                    <p class="text-muted small">Average Rating (out of 4)</p>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="d-flex flex-column align-items-center">
                            <div class="fw-bold mb-1">{{ $assignment->kadiv_rating }}/4</div>
                            <div class="progress w-75 mb-1" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($assignment->kadiv_rating/4)*100 }}%"></div>
                            </div>
                            <div class="text-muted small">Kadiv</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column align-items-center">
                            <div class="fw-bold mb-1">{{ $assignment->director_rating }}/4</div>
                            <div class="progress w-75 mb-1" style="height: 5px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($assignment->director_rating/4)*100 }}%"></div>
                            </div>
                            <div class="text-muted small">Director</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array($assignment->status, ['rejected_kadiv', 'rejected_director']))
        <!-- Revision Required Card -->
        <div class="card mb-4" style="height: auto">
            <div class="card-header bg-danger-light">
                <h5 class="card-header-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Revision Required
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $assignment->status == 'rejected_kadiv' ? 'bg-primary-light text-primary' : 'bg-info-light text-info' }} py-1 px-2">
                        <i class="fas {{ $assignment->status == 'rejected_kadiv' ? 'fa-user-check' : 'fa-user-tie' }} me-1"></i>
                        {{ $assignment->status == 'rejected_kadiv' ? 'Kepala Divisi' : 'Direktur' }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="fw-bold mb-1">Feedback:</div>
                    <p class="mb-0">
                        @if($assignment->status == 'rejected_kadiv')
                            {{ $assignment->kadiv_notes ?: 'No specific feedback provided.' }}
                        @else
                            {{ $assignment->director_notes ?: 'No specific feedback provided.' }}
                        @endif
                    </p>
                </div>

                <div class="alert alert-warning py-2 d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>Please revise and resubmit your work.</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection