@extends('layouts.head_division')

@section('title', 'Anggota Tim')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Anggota Tim</h2>
        <p class="text-secondary mb-0">Kelola dan pantau semua anggota tim dalam divisi Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('head_division.performances.compare') }}" class="btn btn-info">
            <i class="fas fa-chart-bar me-2"></i>Bandingkan Kinerja
        </a>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Total Anggota</h6>
                        <h2 class="mb-0">{{ $stats['total_members'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-award fa-3x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Total Penghargaan</h6>
                        <h2 class="mb-0">{{ $stats['total_awards'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Rata-Rata Kinerja</h6>
                        <h2 class="mb-0">{{ number_format($stats['avg_performance'] ?? 0, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Pengajuan Tertunda</h6>
                        <h2 class="mb-0">{{ $stats['pending_promotions'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Members Card -->
<div class="card h-100 mb-4">
    <div class="card-header">
        <h5 class="card-header-title">
            <i class="fas fa-users me-2 text-primary"></i>
            Daftar Anggota Tim
        </h5>
        <div class="card-header-actions">
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-users me-1"></i>
                {{ $teamMembers->count() }} Total Anggota
            </span>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover" id="team-members-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Penghargaan</th>
                        <th>Total Tugas</th>
                        <th>Tugas Selesai</th>
                        <th>Nilai Kinerja</th>
                        <th>Kategori</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teamMembers as $member)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ $member->name }}&background=random" class="rounded-circle me-2" width="40" height="40" alt="{{ $member->name }}">
                                    <div>
                                        <div class="fw-semibold">{{ $member->name }}</div>
                                        @if($member->latest_award)
                                            <small class="text-success">
                                                <i class="fas fa-award"></i> Penghargaan terakhir: {{ $member->latest_award->reviewed_at->format('M Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->email }}</td>
                            <td>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-award me-1"></i>
                                    {{ $member->approved_promotions }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-tasks me-1"></i>
                                    {{ $member->total_assignments }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $member->completed_assignments }}
                                </span>
                            </td>
                            <td>
                                @if ($member->performance_score)
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $percentage = ($member->performance_score / 4) * 100;
                                            $colorClass = 'bg-danger';

                                            if ($member->performance_score >= 3.7) {
                                                $colorClass = 'bg-success';
                                            } elseif ($member->performance_score >= 3) {
                                                $colorClass = 'bg-info';
                                            } elseif ($member->performance_score >= 2.5) {
                                                $colorClass = 'bg-primary';
                                            } elseif ($member->performance_score >= 2) {
                                                $colorClass = 'bg-warning';
                                            }
                                        @endphp

                                        <div class="progress-bar {{ $colorClass }}" role="progressbar"
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $member->performance_score }}"
                                            aria-valuemin="0"
                                            aria-valuemax="4">
                                            {{ $member->performance_score }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Belum ada penilaian</span>
                                @endif
                            </td>
                            <td>
                                @if ($member->performance_score)
                                    @php
                                        $badgeClass = 'bg-danger';

                                        if ($member->performance_score >= 3.7) {
                                            $badgeClass = 'bg-success';
                                        } elseif ($member->performance_score >= 3) {
                                            $badgeClass = 'bg-info';
                                        } elseif ($member->performance_score >= 2.5) {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($member->performance_score >= 2) {
                                            $badgeClass = 'bg-warning';
                                        }
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $member->performance_category }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('head_division.team_members.show', $member->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="{{ route('head_division.performances.show', $member->id) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-chart-line me-1"></i> Kinerja
                                    </a>

                                    @if ($member->performance_score >= 3 && !$member->promotionRequests()->where('status', 'pending')->exists())
                                    <a href="{{ route('head_division.performances.propose_promotion', $member->id) }}"
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-award me-1"></i> Ajukan Promosi
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Tidak ada anggota tim</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables with enhanced options
        $('#team-members-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "responsive": true,
            "pageLength": 10,  // Display 10 rows per page
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            "search": {
                "smart": true,
                "caseInsensitive": true
            },
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }  // Disable sorting on action column
            ],
            "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            "initComplete": function() {
                // Customize search input
                $('.dataTables_filter input').attr('placeholder', 'Cari anggota tim...');
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_filter label').addClass('mb-0');

                // Customize length menu
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    });
</script>
@endpush
@endsection