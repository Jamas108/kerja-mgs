@extends('layouts.employee')

@section('title', 'Certificate Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">Certificate Details</h3>
                        <p class="text-muted mb-0">View your achievement certificate</p>
                    </div>
                </div>
                <a href="{{ route('employee.achievements.index') }}" class="btn btn-light shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Achievements
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Certificate Viewer Column -->
        <div class="col-lg-8 mb-4">
            <!-- PDF Viewer Card -->
            <div class="card certificate-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf me-2"></i>
                        <h5 class="mb-0">Certificate Preview</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="certificate-viewer">
                        <iframe
                            src="{{ Storage::url($achievement->certificate_file) }}"
                            frameborder="0"
                            class="certificate-iframe"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1 fw-bold">Promotion Certificate</h5>
                            <p class="text-muted small mb-0">
                                <i class="far fa-calendar-check me-1"></i>
                                Awarded on {{ $achievement->reviewed_at ? $achievement->reviewed_at->format('d F Y') : 'N/A' }}
                            </p>
                        </div>
                        <a href="{{ route('employee.achievements.download', $achievement) }}"
                           class="btn btn-primary btn-download">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Information Column -->
        <div class="col-lg-4 mb-4">
            <div class="card info-card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <h5 class="mb-0">Certificate Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Certificate Type -->
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Certificate Type</div>
                            <div class="info-value">Promotion Achievement</div>
                        </div>
                    </div>

                    <!-- Certificate Number -->
                    {{-- <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Certificate Number</div>
                            <div class="info-value">
                                PROM/{{ date('Y', strtotime($achievement->reviewed_at)) }}/{{ str_pad($achievement->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div> --}}

                    <!-- Issued Date -->
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Issued Date</div>
                            <div class="info-value">
                                {{ $achievement->reviewed_at ? $achievement->reviewed_at->format('d F Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Period -->
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Assessment Period</div>
                            <div class="info-value">{{ $achievement->period ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Approved By -->
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Approved By</div>
                            <div class="info-value">{{ $achievement->requester->name ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="info-item border-0 mb-0">
                        <div class="info-icon">
                            <i class="far fa-comment-dots"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Notes</div>
                            <div class="info-value">
                                {{ $achievement->director_notes ?? 'No additional notes' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card-footer bg-white border-0">
                    <div class="d-grid gap-2">
                        <a href="{{ route('employee.achievements.download', $achievement) }}"
                           class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download Certificate
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Certificate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Icon Wrapper */
    .icon-wrapper {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    /* Certificate Card */
    .certificate-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Certificate Viewer */
    .certificate-viewer {
        background: #f8fafc;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .certificate-iframe {
        width: 100%;
        height: 600px;
        border: none;
    }

    /* Download Button */
    .btn-download {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 600;
        padding: 10px 24px;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        background: linear-gradient(135deg, #5568d3 0%, #6941a3 100%);
    }

    /* Info Card */
    .info-card {
        border-radius: 16px;
    }

    .info-item {
        display: flex;
        padding: 16px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .info-icon i {
        color: #667eea;
        font-size: 18px;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 15px;
        color: #1f2937;
        font-weight: 600;
        word-wrap: break-word;
    }

    /* Back Button */
    .btn-light {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Primary Button */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5568d3 0%, #6941a3 100%);
    }

    /* Outline Secondary Button */
    .btn-outline-secondary {
        border-width: 2px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .certificate-iframe {
            height: 500px;
        }

        .certificate-viewer {
            min-height: 500px;
        }
    }

    @media (max-width: 768px) {
        .icon-wrapper {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .certificate-iframe {
            height: 400px;
        }

        .certificate-viewer {
            min-height: 400px;
        }

        .info-icon {
            width: 35px;
            height: 35px;
        }

        .info-icon i {
            font-size: 16px;
        }

        .info-label {
            font-size: 12px;
        }

        .info-value {
            font-size: 14px;
        }
    }

    /* Print Styles */
    @media print {
        .btn, .icon-wrapper, .card-header, .card-footer {
            display: none !important;
        }

        .certificate-card {
            box-shadow: none !important;
        }

        .certificate-iframe {
            height: 100vh;
        }
    }
</style>
@endpush