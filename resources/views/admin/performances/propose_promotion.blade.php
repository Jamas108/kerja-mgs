@extends('layouts.admin')

@section('title', 'Propose Employee Promotion')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Propose Employee Promotion</h2>
        <p class="text-secondary mb-0">Submit promotion proposal for high-performing employee</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.performances.show', $employee->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Employee Details
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Employee Profile
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="https://ui-avatars.com/api/?name={{ $employee->name }}&background=random&size=100"
                     class="rounded-circle mb-3"
                     alt="{{ $employee->name }}">
                <h4 class="fw-bold mb-1">{{ $employee->name }}</h4>
                <p class="text-muted mb-2">{{ $employee->email }}</p>
                <span class="badge bg-info mb-3">{{ $employee->division->name ?? 'No Division' }}</span>

                @if ($performanceScore)
                    <div class="mt-4">
                        <h2 class="display-4 mb-0">{{ $performanceScore }}</h2>
                        <p class="lead">out of 4.00</p>

                        @php
                            $badgeClass = 'bg-danger';
                            $category = 'Poor';

                            if ($performanceScore >= 3.7) {
                                $badgeClass = 'bg-success';
                                $category = 'Excellent';
                            } elseif ($performanceScore >= 3) {
                                $badgeClass = 'bg-info';
                                $category = 'Good';
                            } elseif ($performanceScore >= 2.5) {
                                $badgeClass = 'bg-primary';
                                $category = 'Average';
                            } elseif ($performanceScore >= 2) {
                                $badgeClass = 'bg-warning';
                                $category = 'Below Average';
                            }
                        @endphp

                        <h4><span class="badge {{ $badgeClass }}">{{ $category }}</span></h4>
                    </div>
                @else
                    <div class="mt-4">
                        <h4><span class="badge bg-secondary">No Performance Rating</span></h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-award me-2 text-warning"></i>
                    Promotion Proposal Form
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-1">Important Note:</p>
                        <p class="mb-0">Promotion proposals can only be submitted for employees with "Good" or "Excellent" performance ratings. The proposal will be forwarded to the Director for final approval.</p>
                    </div>
                </div>

                @if($performanceScore && $performanceScore >= 3)
                    <form method="POST" action="{{ route('admin.performances.store_promotion', $employee->id) }}" enctype="multipart/form-data" class="mt-4">
                        @csrf

                        <div class="mb-4">
                            <label for="period" class="form-label fw-bold">Evaluation Period</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="period_start" class="form-label text-muted small">From Month</label>
                                    <input type="month" id="period_start" name="period_start" class="form-control @error('period_start') is-invalid @enderror" value="{{ old('period_start') }}" required>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="period_end" class="form-label text-muted small">To Month</label>
                                    <input type="month" id="period_end" name="period_end" class="form-control @error('period_end') is-invalid @enderror" value="{{ old('period_end') }}" required>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>
                                Select the evaluation period for this promotion proposal (e.g., January 2024 - August 2024)
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="reason" class="form-label fw-bold">Promotion Justification</label>
                            <textarea id="reason" name="reason" rows="6" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                            <div class="form-text">
                                <i class="fas fa-lightbulb text-warning me-1"></i>
                                Provide detailed justification for why this employee deserves a promotion. Include specific achievements, performance improvements, and valuable contributions they have made to the organization.
                            </div>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="supporting_document" class="form-label fw-bold">Supporting Documents (Optional)</label>
                            <input type="file" id="supporting_document" name="supporting_document" class="form-control @error('supporting_document') is-invalid @enderror">
                            <div class="form-text">
                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                Upload supporting documents if available (PDF, DOC, DOCX format, max 2MB)
                            </div>
                            @error('supporting_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.performances.show', $employee->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Promotion Proposal
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning d-flex mt-4" role="alert">
                        <div class="alert-icon me-3">
                            <i class="fas fa-exclamation-triangle fs-4"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-1">Promotion Not Available</p>
                            <p class="mb-0">
                                This employee does not meet the minimum performance requirements for promotion.
                                Employees must have a performance rating of at least 3.0 (Good) to be eligible for promotion.
                                @if($performanceScore)
                                    Current rating: {{ $performanceScore }}/4.0
                                @else
                                    This employee has not been rated yet.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default period values
        const today = new Date();
        const sixMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 6, 1);

        document.getElementById('period_start').value = sixMonthsAgo.toISOString().slice(0, 7);
        document.getElementById('period_end').value = today.toISOString().slice(0, 7);

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const reason = document.getElementById('reason').value.trim();

                if (reason.length < 50) {
                    e.preventDefault();
                    alert('Please provide a more detailed justification (at least 50 characters)');
                    document.getElementById('reason').focus();
                    return false;
                }

                return confirm('Are you sure you want to submit this promotion proposal? This action cannot be undone.');
            });
        }

        // Character counter for reason textarea
        const reasonTextarea = document.getElementById('reason');
        if (reasonTextarea) {
            const counterDiv = document.createElement('div');
            counterDiv.className = 'form-text text-end';
            counterDiv.id = 'reason-counter';
            reasonTextarea.parentNode.appendChild(counterDiv);

            function updateCounter() {
                const length = reasonTextarea.value.length;
                counterDiv.textContent = `${length}/2000 characters`;

                if (length < 50) {
                    counterDiv.className = 'form-text text-end text-danger';
                } else if (length > 1800) {
                    counterDiv.className = 'form-text text-end text-warning';
                } else {
                    counterDiv.className = 'form-text text-end text-muted';
                }
            }

            reasonTextarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endpush
@endsection