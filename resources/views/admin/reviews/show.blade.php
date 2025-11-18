@extends('layouts.admin')

@section('title', 'Review Assignment')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Assignment</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reviews
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Assignment Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assignment Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Task Title:</label>
                            <p class="mb-0">{{ $assignment->jobDesk->title }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Division:</label>
                            <p class="mb-0">{{ $assignment->jobDesk->division->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Employee:</label>
                            <p class="mb-0">{{ $assignment->employee->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Status:</label>
                            <p class="mb-0">{!! $assignment->status_badge !!}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Assigned At:</label>
                            <p class="mb-0">{{ $assignment->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($assignment->completed_at)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Completed At:</label>
                            <p class="mb-0">{{ $assignment->completed_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($assignment->jobDesk->description)
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-800">Task Description:</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($assignment->jobDesk->description)) !!}
                        </div>
                    </div>
                    @endif

                    @if($assignment->evidence_note)
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-800">Employee Evidence/Notes:</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($assignment->evidence_note)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Previous Reviews -->
            @if($assignment->kadiv_rating || $assignment->kadiv_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Head of Division Review</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($assignment->kadiv_rating)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Rating:</label>
                            <p class="mb-0">
                                <span class="badge badge-success badge-lg">{{ $assignment->kadiv_rating }} / 4</span>
                            </p>
                        </div>
                        @endif
                        @if($assignment->kadiv_reviewed_at)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Reviewed At:</label>
                            <p class="mb-0">{{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($assignment->kadiv_notes)
                    <div class="mb-0">
                        <label class="font-weight-bold text-gray-800">Notes:</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($assignment->kadiv_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Director Review (if already reviewed) -->
            @if($assignment->director_rating || $assignment->director_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold {{ $assignment->status === 'final' ? 'text-success' : 'text-danger' }}">
                        Director Review
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($assignment->director_rating)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Rating:</label>
                            <p class="mb-0">
                                <span class="badge {{ $assignment->status === 'final' ? 'badge-success' : 'badge-danger' }} badge-lg">
                                    {{ $assignment->director_rating }} / 4
                                </span>
                            </p>
                        </div>
                        @endif
                        @if($assignment->director_reviewed_at)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800">Reviewed At:</label>
                            <p class="mb-0">{{ $assignment->director_reviewed_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($assignment->director_notes)
                    <div class="mb-0">
                        <label class="font-weight-bold text-gray-800">Notes:</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($assignment->director_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Review Form -->
        <div class="col-lg-4">
            @if($assignment->status === 'in_review_director')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Submit Director Review</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reviews.review', $assignment) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="rating" class="font-weight-bold">Director Rating <span class="text-danger">*</span></label>
                            <select class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Poor</option>
                                <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Fair</option>
                                <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Good</option>
                                <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Excellent</option>
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes" class="font-weight-bold">Director Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="4"
                                      placeholder="Enter your review notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="decision" class="font-weight-bold">Decision <span class="text-danger">*</span></label>
                            <select class="form-control @error('decision') is-invalid @enderror" id="decision" name="decision" required>
                                <option value="">Select Decision</option>
                                <option value="approve" {{ old('decision') == 'approve' ? 'selected' : '' }}>
                                    <i class="fas fa-check"></i> Approve (Final)
                                </option>
                                <option value="reject" {{ old('decision') == 'reject' ? 'selected' : '' }}>
                                    <i class="fas fa-times"></i> Reject
                                </option>
                            </select>
                            @error('decision')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Assignment Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Assignment Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Assigned -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Task Assigned</h6>
                                <small class="text-muted">{{ $assignment->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>

                        <!-- Completed -->
                        @if($assignment->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Task Completed</h6>
                                <small class="text-muted">{{ $assignment->completed_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        @endif

                        <!-- Head Division Review -->
                        @if($assignment->kadiv_reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Head Division Review</h6>
                                <small class="text-muted">{{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}</small>
                                @if($assignment->kadiv_rating)
                                <div class="mt-1">
                                    <span class="badge badge-info">{{ $assignment->kadiv_rating }}/4</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Director Review -->
                        @if($assignment->director_reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $assignment->status === 'final' ? 'bg-success' : 'bg-danger' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Director Review</h6>
                                <small class="text-muted">{{ $assignment->director_reviewed_at->format('d M Y H:i') }}</small>
                                @if($assignment->director_rating)
                                <div class="mt-1">
                                    <span class="badge {{ $assignment->status === 'final' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $assignment->director_rating }}/4
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @elseif($assignment->status === 'in_review_director')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Awaiting Director Review</h6>
                                <small class="text-muted">Current Status</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .timeline-content {
        padding-left: 10px;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    @media (max-width: 767.98px) {
        .d-grid .btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('form').submit(function(e) {
        var rating = $('#rating').val();
        var decision = $('#decision').val();

        if (!rating || !decision) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        return confirm('Are you sure you want to submit this review? This action cannot be undone.');
    });

    // Decision change handler
    $('#decision').change(function() {
        var decision = $(this).val();
        var $submitBtn = $('button[type="submit"]');

        if (decision === 'approve') {
            $submitBtn.removeClass('btn-danger').addClass('btn-primary');
            $submitBtn.html('<i class="fas fa-check"></i> Approve Review');
        } else if (decision === 'reject') {
            $submitBtn.removeClass('btn-primary').addClass('btn-danger');
            $submitBtn.html('<i class="fas fa-times"></i> Reject Review');
        } else {
            $submitBtn.removeClass('btn-danger').addClass('btn-primary');
            $submitBtn.html('<i class="fas fa-paper-plane"></i> Submit Review');
        }
    });
});
</script>
@endpush
@endsection