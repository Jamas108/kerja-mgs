@extends('layouts.employee')

@section('title', 'My Achievements')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-header-title">
                        <i class="fas fa-award text-primary me-2"></i>My Certificates & Achievements
                    </h5>
                </div>
            </div>
            <div class="card-body">
                @if($achievements->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-certificate text-secondary" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">No Certificates Found</h5>
                        <p class="text-muted">
                            You don't have any certificates or achievements yet.
                            Keep up the good work!
                        </p>
                    </div>
                @else
                    <div class="row">
                        @foreach($achievements as $achievement)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        <div class="certificate-preview bg-light text-center py-5">
                                            <i class="fas fa-certificate text-primary" style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Promotion Certificate</h5>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $achievement->reviewed_at ? $achievement->reviewed_at->format('d M Y') : 'N/A' }}
                                            </small>
                                        </p>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                Period: {{ $achievement->period ?? 'N/A' }}
                                            </small>
                                        </p>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-user me-1"></i>
                                                Approved by: {{ $achievement->requester->name ?? 'N/A' }}
                                            </small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                        <a href="{{ route('employee.achievements.show', $achievement) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <a href="{{ route('employee.achievements.download', $achievement) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .certificate-preview {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: var(--card-border-radius) var(--card-border-radius) 0 0;
    }

    .certificate-preview i {
        opacity: 0.5;
    }

    .card:hover .certificate-preview i {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }
</style>
@endpush