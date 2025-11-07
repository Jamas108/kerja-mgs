@extends('layouts.head_division')

@section('title', 'Anggota Tim')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Responsive Cards */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 1rem;
        }

        .stats-card .card-body {
            padding: 1rem;
        }

        .stats-card i {
            font-size: 2rem !important;
        }

        .stats-card h2 {
            font-size: 1.5rem;
        }

        .stats-card h6 {
            font-size: 0.75rem;
        }
    }

    /* Responsive Header */
    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .header-buttons {
            flex-direction: column;
            width: 100%;
        }

        .header-buttons .btn {
            width: 100%;
        }
    }

    /* Responsive Table */
    @media (max-width: 992px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td, .table th {
            white-space: nowrap;
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .badge {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        /* Mobile table adjustments */
        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background: #fff;
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border: none;
            white-space: normal;
        }

        .table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
            flex: 0 0 40%;
        }

        .table tbody td:first-child {
            border-top: none;
        }

        .action-buttons {
            flex-direction: column !important;
            width: 100%;
            gap: 0.5rem !important;
        }

        .action-buttons .btn {
            width: 100%;
        }

        /* Avatar adjustments */
        .user-info {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem;
        }

        .user-info img {
            width: 32px !important;
            height: 32px !important;
        }
    }

    /* Card Header Responsive */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .card-header-actions {
            width: 100%;
        }

        .card-header-title {
            font-size: 1rem;
        }
    }

    /* DataTables Responsive */
    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
        }

        .dataTables_wrapper .d-flex {
            flex-direction: column;
            align-items: stretch !important;
        }
    }

    /* Progress bar mobile */
    @media (max-width: 576px) {
        .progress {
            min-width: 100px;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Halaman -->
<div class="page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Anggota Tim</h2>
        <p class="text-secondary mb-0">Kelola dan pantau semua anggota tim dalam divisi Anda</p>
    </div>
    <div class="header-buttons d-flex gap-2">
        <a href="{{ route('head_division.performances.compare') }}" class="btn btn-info">
            <i class="fas fa-chart-bar me-2"></i>Bandingkan Kinerja
        </a>
    </div>
</div>

<!-- Kartu Statistik -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card bg-primary text-white stats-card">
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

    <div class="col-md-6 col-lg-3">
        <div class="card bg-success text-white stats-card">
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

    <div class="col-md-6 col-lg-3">
        <div class="card bg-info text-white stats-card">
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

    <div class="col-md-6 col-lg-3">
        <div class="card bg-warning text-dark stats-card">
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

<!-- Kartu Daftar Anggota Tim -->
<div class="card h-100 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-header-title mb-0">
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
                            <td data-label="No">{{ $loop->iteration }}</td>
                            <td data-label="Nama">
                                <div class="d-flex align-items-center user-info">
                                    <div>
                                        <div class="fw-semibold">{{ $member->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Email">{{ $member->email }}</td>
                            <td data-label="Penghargaan">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-award me-1"></i>
                                    {{ $member->approved_promotions }}
                                </span>
                            </td>
                            <td data-label="Total Tugas">
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-tasks me-1"></i>
                                    {{ $member->total_assignments }}
                                </span>
                            </td>
                            <td data-label="Tugas Selesai">
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $member->completed_assignments }}
                                </span>
                            </td>
                            <td data-label="Nilai Kinerja">
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
                            <td data-label="Kategori">
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
                            <td data-label="Aksi">
                                <div class="d-flex gap-2 action-buttons">
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
<!-- jQuery (required untuk DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables dengan opsi yang ditingkatkan
        $('#team-members-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json",
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "zeroRecords": "Tidak ada data yang cocok",
                "emptyTable": "Tidak ada data tersedia",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            },
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            "search": {
                "smart": true,
                "caseInsensitive": true
            },
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ],
            "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            "initComplete": function() {
                // Kustomisasi input pencarian
                $('.dataTables_filter input').attr('placeholder', 'Cari anggota tim...');
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_filter label').addClass('mb-0');

                // Kustomisasi menu panjang
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    });
</script>
@endpush
@endsection