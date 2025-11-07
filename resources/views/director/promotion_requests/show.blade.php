@extends('layouts.director')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Detail Pengajuan Promosi</h5>
                            <a href="{{ route('director.promotion_requests.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Employee Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informasi Karyawan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Nama</th>
                                                <td>{{ $promotionRequest->employee->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $promotionRequest->employee->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Divisi</th>
                                                <td>{{ $promotionRequest->employee->division->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Periode Penilaian</th>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ $promotionRequest->period ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informasi Pengajuan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Diajukan Oleh</th>
                                                <td>{{ $promotionRequest->requester->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Pengajuan</th>
                                                <td>{{ $promotionRequest->created_at->format('d M Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>{!! $promotionRequest->status_badge !!}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Promotion Reason -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Alasan Pengajuan Promosi</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-justify">{{ $promotionRequest->reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Document -->
                        @if ($promotionRequest->supporting_document)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Dokumen Pendukung</h6>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ Storage::url($promotionRequest->supporting_document) }}"
                                                target="_blank" class="btn btn-info">
                                                <i class="fas fa-file-download"></i> Unduh Dokumen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Performance Chart (jika ada data) -->
                        @if (isset($monthlyPerformance) && count($monthlyPerformance) > 0)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Grafik Performa Karyawan</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="performanceChart" style="max-height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Approval/Rejection Section -->
                        @if ($promotionRequest->status == 'pending')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">Tanggapan Terhadap Pengajuan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card border-success mb-3">
                                                        <div class="card-header bg-success text-white">
                                                            <h6 class="mb-0">Setujui Pengajuan Promosi</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <form
                                                                action="{{ route('director.promotion_requests.approve', $promotionRequest) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="notes" class="form-label">Catatan
                                                                        (opsional)</label>
                                                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                                </div>

                                                                <!-- Signature Selection -->
                                                                <div class="mb-4">
                                                                    <label for="signature_id" class="form-label">Pilih Tanda
                                                                        Tangan <span class="text-danger">*</span></label>
                                                                    <div class="row">
                                                                        @foreach ($activeSignatures as $signature)
                                                                            <div class="col-md-6 mb-3">
                                                                                <div
                                                                                    class="card h-100 signature-card {{ $signature->user_id == Auth::id() ? 'border-primary' : '' }}">
                                                                                    <div class="card-body text-center">
                                                                                        <img src="{{ $signature->image_url }}"
                                                                                            alt="Tanda Tangan {{ $signature->title }}"
                                                                                            class="img-thumbnail mb-2"
                                                                                            style="max-height: 80px;">
                                                                                        <div class="form-check">
                                                                                            <input
                                                                                                class="form-check-input signature-radio"
                                                                                                type="radio"
                                                                                                name="signature_id"
                                                                                                id="signature_{{ $signature->id }}"
                                                                                                value="{{ $signature->id }}"
                                                                                                data-promotion-id="{{ $promotionRequest->id }}"
                                                                                                {{ $signature->user_id == Auth::id() ? 'checked' : '' }}>
                                                                                            <label class="form-check-label"
                                                                                                for="signature_{{ $signature->id }}">
                                                                                                {{ $signature->title }}
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <!-- Certificate Preview -->
                                                                <div class="mb-3">
                                                                    <div class="d-flex justify-content-between">
                                                                        <label class="form-label">Preview
                                                                            Sertifikat:</label>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-primary preview-certificate">
                                                                            <i class="fas fa-eye"></i> Lihat Preview
                                                                        </button>
                                                                    </div>
                                                                    <small class="form-text text-muted">Sertifikat akan
                                                                        digenerate otomatis saat promosi disetujui.</small>
                                                                </div>

                                                                <button type="submit" class="btn btn-success w-100">
                                                                    <i class="fas fa-check-circle"></i> Setujui Promosi
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border-danger mb-3">
                                                        <div class="card-header bg-danger text-white">
                                                            <h6 class="mb-0">Tolak Pengajuan Promosi</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <form
                                                                action="{{ route('director.promotion_requests.reject', $promotionRequest) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="notes" class="form-label">Alasan
                                                                        Penolakan <span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                                                        required></textarea>
                                                                    @error('notes')
                                                                        <div class="invalid-feedback">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <button type="submit" class="btn btn-danger w-100">
                                                                    <i class="fas fa-times-circle"></i> Tolak Promosi
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate Preview Modal -->
                            <div class="modal fade" id="previewCertificateModal" tabindex="-1"
                                aria-labelledby="previewCertificateModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="previewCertificateModalLabel">Preview Sertifikat
                                                Promosi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="certificate-preview">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p>Memuat preview sertifikat...</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($promotionRequest->status == 'approved' || $promotionRequest->status == 'rejected')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">Catatan Keputusan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Status:</strong> {!! $promotionRequest->status_badge !!}</p>
                                                    <p><strong>Diproses pada:</strong>
                                                        {{ $promotionRequest->reviewed_at ? $promotionRequest->reviewed_at->format('d M Y H:i') : '-' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Catatan Direktur:</strong></p>
                                                    <p>{{ $promotionRequest->director_notes ?: 'Tidak ada catatan' }}</p>
                                                </div>
                                            </div>

                                            @if ($promotionRequest->certificate_file && $promotionRequest->status == 'approved')
                                                <hr>
                                                <div class="text-center">
                                                    <h6>Sertifikat Penghargaan</h6>
                                                    <a href="{{ route('director.promotion_requests.download_certificate', $promotionRequest) }}"
                                                        class="btn btn-primary mt-2">
                                                        <i class="fas fa-file-download"></i> Unduh Sertifikat
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const monthlyPerformance = @json($monthlyPerformance ?? []);

                // Chart JS code for performance visualization
                if (monthlyPerformance && monthlyPerformance.length > 0) {
                    const ctx = document.getElementById('performanceChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: monthlyPerformance.map(item => item.period),
                                datasets: [{
                                    label: 'Performance Score',
                                    data: monthlyPerformance.map(item => item.average_score),
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 4
                                    }
                                }
                            }
                        });
                    }
                }

                // Certificate preview functionality
                $('.preview-certificate').on('click', function(e) {
                    e.preventDefault();

                    const selectedSignature = $('input[name="signature_id"]:checked');
                    if (selectedSignature.length === 0) {
                        alert('Silakan pilih tanda tangan terlebih dahulu');
                        return;
                    }

                    const signatureId = selectedSignature.val();
                    const promotionId = selectedSignature.data('promotion-id');

                    // Show the modal
                    $('#previewCertificateModal').modal('show');

                    // Load certificate preview
                    $.ajax({
                        url: '{{ route('director.promotion_requests.preview-certificate') }}',
                        type: 'POST',
                        data: {
                            promotion_request_id: promotionId,
                            signature_id: signatureId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#certificate-preview').html(response);
                        },
                        error: function() {
                            $('#certificate-preview').html(
                                '<div class="alert alert-danger text-center">Gagal memuat preview sertifikat</div>'
                            );
                        }
                    });
                });

                // Style selected signature card
                $('.signature-radio').on('change', function() {
                    $('.signature-card').removeClass('border-primary');
                    $(this).closest('.signature-card').addClass('border-primary');
                });
            });
        </script>
    @endpush
@endsection
