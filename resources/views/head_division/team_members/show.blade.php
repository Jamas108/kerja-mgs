@extends('layouts.head_division')

@section('title', 'Detail Anggota Tim')

@push('styles')
<style>
    /* Responsive Header */
    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .card-header .btn-group {
            flex-direction: column;
            width: 100%;
        }

        .card-header .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }

    /* Profile Card Responsive */
    @media (max-width: 992px) {
        .profile-stats {
            flex-direction: column;
        }

        .profile-stats > div {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0 !important;
        }

        .profile-stats > div:last-child {
            border-bottom: none;
        }
    }

    /* Table Responsive */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td, .table th {
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Mobile Table Cards */
    @media (max-width: 576px) {
        .awards-table thead {
            display: none;
        }

        .awards-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background: #fff;
        }

        .awards-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border: none;
        }

        .awards-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
            flex: 0 0 40%;
        }
    }

    /* Chart Container */
    @media (max-width: 768px) {
        #performanceChart {
            max-height: 250px;
        }
    }

    /* List Group Responsive */
    @media (max-width: 576px) {
        .list-group-item {
            padding: 1rem 0.75rem;
        }

        .list-group-item h6 {
            font-size: 0.875rem;
        }

        .list-group-item small {
            font-size: 0.75rem;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #6c757d;
    }

    @media (max-width: 576px) {
        .empty-state {
            padding: 2rem 1rem;
        }

        .empty-state i {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Anggota Tim</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Profil Karyawan -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teamMember->name) }}&background=random&size=150"
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="{{ $teamMember->name }}" />
                                    <h4 class="mb-1">{{ $teamMember->name }}</h4>
                                    <p class="text-muted mb-3">{{ $teamMember->email }}</p>

                                    <div class="d-flex justify-content-center mb-3 profile-stats">
                                        <div class="px-3 border-end">
                                            <h5 class="mb-0">{{ $teamMember->awards_count ?? 0 }}</h5>
                                            <small class="text-muted">Penghargaan</small>
                                        </div>
                                        <div class="px-3 border-end">
                                            <h5 class="mb-0">{{ $teamMember->assignedJobs->where('status', 'final')->count() }}</h5>
                                            <small class="text-muted">Tugas Selesai</small>
                                        </div>
                                        <div class="px-3">
                                            <h5 class="mb-0">{{ $teamMember->performance_score ? number_format($teamMember->performance_score, 2) : '-' }}</h5>
                                            <small class="text-muted">Nilai Kinerja</small>
                                        </div>
                                    </div>

                                    @if($teamMember->performance_score)
                                    <div class="mb-2">
                                        @php
                                            $badgeClass = 'bg-danger';

                                            if ($teamMember->performance_score >= 3.7) {
                                                $badgeClass = 'bg-success';
                                            } elseif ($teamMember->performance_score >= 3) {
                                                $badgeClass = 'bg-info';
                                            } elseif ($teamMember->performance_score >= 2.5) {
                                                $badgeClass = 'bg-primary';
                                            } elseif ($teamMember->performance_score >= 2) {
                                                $badgeClass = 'bg-warning';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2">
                                            {{ $teamMember->performance_category }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Penghargaan -->
                        <div class="col-lg-8 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-award me-2"></i> Daftar Penghargaan</h5>
                                </div>
                                <div class="card-body">
                                    @if($awards->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table awards-table">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Diajukan Oleh</th>
                                                        <th>Alasan</th>
                                                        <th>Sertifikat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($awards as $award)
                                                    <tr>
                                                        <td data-label="Tanggal">
                                                            {{ $award->reviewed_at ? $award->reviewed_at->format('d M Y') : '-' }}
                                                        </td>
                                                        <td data-label="Diajukan Oleh">
                                                            {{ $award->requester->name ?? '-' }}
                                                        </td>
                                                        <td data-label="Alasan">
                                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reasonModal{{ $award->id }}">
                                                                Lihat Alasan
                                                            </button>

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="reasonModal{{ $award->id }}" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Alasan Pengajuan</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <h6 class="mb-2">Alasan:</h6>
                                                                            <p class="mb-3">{{ $award->reason ?? 'Tidak ada alasan' }}</p>
                                                                            <hr>
                                                                            <h6 class="mb-2">Catatan Direktur:</h6>
                                                                            <p class="mb-0">{{ $award->director_notes ?? 'Tidak ada catatan' }}</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td data-label="Sertifikat">
                                                            @if($award->certificate_file)
                                                                <a href="{{ Storage::url($award->certificate_file) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i> Unduh
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-trophy text-muted"></i>
                                            <h5 class="text-muted mb-3">Belum Ada Penghargaan</h5>
                                            <p class="text-muted mb-3">Anggota tim ini belum mendapatkan penghargaan</p>
                                            @if ($teamMember->performance_score >= 3)
                                                <a href="{{ route('head_division.performances.propose_promotion', $teamMember->id) }}"
                                                   class="btn btn-success">
                                                    <i class="fas fa-award me-2"></i> Ajukan Promosi
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Grafik Kinerja -->
                        <div class="col-lg-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Grafik Kinerja Bulanan</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tugas Terbaru -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Tugas Terbaru</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($recentAssignments as $assignment)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between mb-2">
                                                    <h6 class="mb-0">{{ $assignment->jobDesk->title ?? 'Tugas' }}</h6>
                                                    <small class="text-muted">
                                                        {{ $assignment->director_reviewed_at ? $assignment->director_reviewed_at->format('d M Y') : '-' }}
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <div>
                                                        <small class="text-muted me-1">Nilai:</small>
                                                        <span class="badge {{ $assignment->performance_badge_color ?? 'bg-secondary' }}">
                                                            {{ $assignment->average_rating ? number_format($assignment->average_rating, 2) : '-' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted me-1">Kategori:</small>
                                                        <span class="badge {{ $assignment->performance_badge_color ?? 'bg-secondary' }}">
                                                            {{ $assignment->performance_category ?? 'Belum dinilai' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="fas fa-tasks text-muted"></i>
                                                <h5 class="text-muted mb-0">Belum Ada Tugas</h5>
                                                <p class="text-muted small mb-0">Belum ada tugas yang selesai</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const performanceHistory = @json($performanceHistory);

        if (performanceHistory && performanceHistory.length > 0) {
            const labels = performanceHistory.map(item => item.period);
            const scoreData = performanceHistory.map(item => parseFloat(item.average_score) || 0);
            const taskData = performanceHistory.map(item => parseInt(item.total_tasks) || 0);

            const ctx = document.getElementById('performanceChart');

            if (ctx) {
                const chart = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Nilai Rata-rata',
                                data: scoreData,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                yAxisID: 'y',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Jumlah Tugas',
                                data: taskData,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                yAxisID: 'y1',
                                type: 'bar'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(2);
                                        }
                                        return label;
                                    }
                                }
                            }
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
                                max: 4,
                                ticks: {
                                    stepSize: 0.5
                                }
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
                                min: 0,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        } else {
            const chartContainer = document.getElementById('performanceChart');
            if (chartContainer) {
                chartContainer.parentNode.innerHTML = '<div class="empty-state"><i class="fas fa-chart-line text-muted"></i><h5 class="text-muted mb-0">Data Tidak Tersedia</h5><p class="text-muted small mb-0">Belum ada data kinerja untuk ditampilkan</p></div>';
            }
        }
    });
</script>
@endpush
@endsection