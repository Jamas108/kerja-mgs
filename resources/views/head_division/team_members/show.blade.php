@extends('layouts.head_division')

@section('title', 'Detail Anggota Tim')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Detail Anggota Tim') }}</h5>
                        <div>
                            <a href="{{ route('head_division.team_members.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            @if ($teamMember->performance_score >= 3 && !$teamMember->promotionRequests()->where('status', 'pending')->exists())
                            <a href="{{ route('head_division.performances.propose_promotion', $teamMember->id) }}"
                               class="btn btn-sm btn-success">
                                <i class="fas fa-award"></i> Ajukan Promosi
                            </a>
                            @endif

                            <a href="{{ route('head_division.performances.show', $teamMember->id) }}"
                               class="btn btn-sm btn-info">
                                <i class="fas fa-chart-line"></i> Detail Kinerja
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Employee Profile -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="https://ui-avatars.com/api/?name={{ $teamMember->name }}&background=random&size=150"
                                         class="rounded-circle mb-3" alt="{{ $teamMember->name }}" />
                                    <h4>{{ $teamMember->name }}</h4>
                                    <p class="text-muted">{{ $teamMember->email }}</p>

                                    <div class="d-flex justify-content-center mb-3">
                                        <div class="px-3 border-end">
                                            <h5 class="mb-0">{{ $teamMember->awards_count }}</h5>
                                            <small class="text-muted">Penghargaan</small>
                                        </div>
                                        <div class="px-3 border-end">
                                            <h5 class="mb-0">{{ $teamMember->assignedJobs->where('status', 'final')->count() }}</h5>
                                            <small class="text-muted">Tugas Selesai</small>
                                        </div>
                                        <div class="px-3">
                                            <h5 class="mb-0">{{ $teamMember->performance_score ?? '-' }}</h5>
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

                        <!-- Awards Section -->
                        <div class="col-md-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-award me-2"></i> Daftar Penghargaan</h5>
                                </div>
                                <div class="card-body">
                                    @if($awards->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table">
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
                                                        <td>{{ $award->reviewed_at->format('d M Y') }}</td>
                                                        <td>{{ $award->requester->name }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-link"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reasonModal{{ $award->id }}">
                                                                Lihat Alasan
                                                            </button>

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="reasonModal{{ $award->id }}" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Alasan Pengajuan</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>{{ $award->reason }}</p>
                                                                            <hr>
                                                                            <h6>Catatan Direktur:</h6>
                                                                            <p>{{ $award->director_notes ?? 'Tidak ada catatan' }}</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($award->certificate_file)
                                                                <a href="{{ Storage::url($award->certificate_file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i> Unduh
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Anggota tim belum mendapatkan penghargaan</h5>
                                            @if ($teamMember->performance_score >= 3)
                                                <a href="{{ route('head_division.performances.propose_promotion', $teamMember->id) }}" class="btn btn-success mt-3">
                                                    <i class="fas fa-award"></i> Ajukan Promosi
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Performance Chart -->
                        <div class="col-md-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Grafik Kinerja Bulanan</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Tasks -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Tugas Terbaru</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($recentAssignments as $assignment)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $assignment->jobDesk->title }}</h6>
                                                    <small>{{ $assignment->director_reviewed_at->format('d M Y') }}</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">Nilai:</small>
                                                        <span class="badge {{ $assignment->performance_badge_color }}">
                                                            {{ number_format($assignment->average_rating, 2) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Kategori:</small>
                                                        <span class="badge {{ $assignment->performance_badge_color }}">
                                                            {{ $assignment->performance_category }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="list-group-item text-center py-5">
                                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                                <p class="mb-0">Belum ada tugas yang selesai</p>
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

        if (performanceHistory.length > 0) {
            const labels = performanceHistory.map(item => item.period);
            const scoreData = performanceHistory.map(item => item.average_score);
            const taskData = performanceHistory.map(item => item.total_tasks);

            const ctx = document.getElementById('performanceChart').getContext('2d');
            const chart = new Chart(ctx, {
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
            document.getElementById('performanceChart').parentNode.innerHTML = '<div class="text-center py-5"><p class="text-muted">Data tidak tersedia</p></div>';
        }
    });
</script>
@endpush
@endsection