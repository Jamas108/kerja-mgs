@extends('layouts.director')

@section('title', 'Review Task')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Task: {{ $assignment->jobDesk->title }}</h1>
        <a href="{{ route('director.reviews.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reviews
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Title</h5>
                        <p>{{ $assignment->jobDesk->title }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Description</h5>
                        <p>{{ $assignment->jobDesk->description }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Deadline</h5>
                        <p>{{ $assignment->jobDesk->deadline->format('d M Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Assigned To</h5>
                        <p>{{ $assignment->employee->name }} ({{ $assignment->employee->email }})</p>
                    </div>
                    <div class="mb-3">
                        <h5>Division</h5>
                        <p>{{ $assignment->jobDesk->division->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Status</h5>
                        <p>{!! $assignment->status_badge !!}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Completed At</h5>
                        <p>{{ $assignment->completed_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Employee Submission</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Evidence Note</h5>
                        <p>{{ $assignment->evidence_note }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Evidence File</h5>
                        @if($assignment->evidence_file)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $assignment->evidence_file) }}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-download"></i> View/Download Evidence
                                </a>
                            </div>
                            @php
                                $fileExtension = pathinfo(storage_path('app/public/' . $assignment->evidence_file), PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                            @endphp

                            @if($isImage)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $assignment->evidence_file) }}" class="img-fluid rounded" alt="Evidence Image">
                                </div>
                            @endif
                        @else
                            <p class="text-muted">No evidence file uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kepala Divisi Review</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Rating</h5>
                        <p>{{ $assignment->kadiv_rating }} / 4</p>
                    </div>
                    <div class="mb-3">
                        <h5>Notes</h5>
                        <p>{{ $assignment->kadiv_notes ?: 'No notes provided.' }}</p>
                    </div>
                    <div class="mb-3">
                        <h5>Reviewed At</h5>
                        <p>{{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Submit Director Review</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('director.reviews.submit', $assignment) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating (1-4)</label>
                            <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Excellent</option>
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Decision</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="approve" value="approve" checked>
                                <label class="form-check-label" for="approve">
                                    Approve and mark as final
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="reject" value="reject">
                                <label class="form-check-label" for="reject">
                                    Reject and return to employee for revision
                                </label>
                            </div>
                            @error('decision')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
