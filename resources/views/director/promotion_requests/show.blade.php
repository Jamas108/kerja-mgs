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
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Informasi Karyawan</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Nama</th>
                                        <td>{{ $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Divisi</th>
                                        <td>{{ $employee->division->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nilai Kinerja</th>
                                        <td>
                                            @if ($performanceScore)
                                                <span
                                                    class="badge
                                                @if ($performanceScore >= 3.7) bg-success
                                                @elseif($performanceScore >= 3) bg-info
                                                @elseif($performanceScore >= 2.5) bg-primary
                                                @elseif($performanceScore >= 2) bg-warning
                                                @else bg-danger @endif
                                                ">{{ $performanceScore }}
                                                    - {{ $performanceCategory }}</span>
                                            @else
                                                <span class="badge bg-secondary">Belum Ada Penilaian</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Detail Pengajuan</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Diajukan Oleh</th>
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
                                    <tr>
                                        <th>Dokumen Pendukung</th>
                                        <td>
                                            @if ($promotionRequest->supporting_document)
                                                <a href="{{ Storage::url($promotionRequest->supporting_document) }}"
                                                    target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-download"></i> Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">Tidak ada dokumen</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Alasan Pengajuan Promosi</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $promotionRequest->reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Chart -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Grafik Kinerja Bulanan</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="performanceChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Assignments -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Tugas Terbaru</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tugas</th>
                                                        <th>Tanggal Selesai</th>
                                                        <th>Penilaian Kadiv</th>
                                                        <th>Penilaian Direktur</th>
                                                        <th>Rata-rata</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($recentAssignments as $assignment)
                                                        <tr>
                                                            <td>{{ $assignment->jobDesk->title }}</td>
                                                            <td>{{ $assignment->completed_at ? $assignment->completed_at->format('d M Y') : '-' }}
                                                            </td>
                                                            <td>
                                                                @if ($assignment->kadiv_rating)
                                                                    <span
                                                                        class="badge bg-primary">{{ $assignment->kadiv_rating }}</span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($assignment->director_rating)
                                                                    <span
                                                                        class="badge bg-primary">{{ $assignment->director_rating }}</span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($assignment->average_rating)
                                                                    <span
                                                                        class="badge {{ $assignment->performance_badge_color }}">{{ number_format($assignment->average_rating, 2) }}</span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-3">Tidak ada tugas yang
                                                                sudah dinilai</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                                method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="notes" class="form-label">Catatan
                                                                        (opsional)</label>
                                                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="certificate_file" class="form-label">Unggah
                                                                        Sertifikat Penghargaan <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="file"
                                                                        class="form-control @error('certificate_file') is-invalid @enderror"
                                                                        id="certificate_file" name="certificate_file"
                                                                        required>
                                                                    <small class="form-text text-muted">Format: PDF, DOC,
                                                                        DOCX, JPG, JPEG, PNG (Maks: 2MB)</small>
                                                                    @error('certificate_file')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
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
                                                                        Penolakan <span class="text-danger">*</span></label>
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
                                                    <a href="{{ Storage::url($promotionRequest->certificate_file) }}"
                                                        target="_blank" class="btn btn-primary mt-2">
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
                const monthlyPerformance = @json($monthlyPerformance);

                if (monthlyPerformance.length > 0) {
                    const labels = monthlyPerformance.map(item => item.period);
                    const scoreData = monthlyPerformance.map(item => item.average_score);
                    const taskData = monthlyPerformance.map(item => item.total_tasks);

                    const ctx = document.getElementById('performanceChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Nilai Rata-rata',
                                    data: scoreData,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    yAxisID: 'y',
                                    tension: 0.3
                                },
                                {
                                    label: 'Jumlah Tugas',
                                    data: taskData,
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    yAxisID: 'y1',
                                    type: 'bar'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Nilai Kinerja'
                                    },
                                    min: 0,
                                    max: 4
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Tugas'
                                    },
                                    min: 0
                                }
                            }
                        }
                    });
                } else {
                    document.getElementById('performanceChart').parentNode.innerHTML =
                        '<div class="text-center py-5"><p class="text-muted">Data tidak tersedia</p></div>';
                }
            });
        </script>
    @endpush
@endsection
