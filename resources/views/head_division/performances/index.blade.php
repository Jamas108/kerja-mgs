@extends('layouts.head_division')

@section('title', 'Kinerja Karyawan')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Kinerja Karyawan</h2>
        <p class="text-secondary mb-0">Pantau dan evaluasi kinerja karyawan dalam divisi Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('head_division.performances.compare') }}" class="btn btn-info">
            <i class="fas fa-chart-bar me-2"></i>Bandingkan Kinerja
        </a>
        <a href="{{ route('head_division.performances.report') }}" class="btn btn-primary">
            <i class="fas fa-file-alt me-2"></i>Laporan Kinerja Divisi
        </a>
    </div>
</div>

<!-- Employee Performance Card -->
<div class="card h-100 mb-4">
    <div class="card-header">
        <h5 class="card-header-title">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Performa Karyawan
        </h5>
        <div class="card-header-actions">
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-users me-1"></i>
                {{ $employees->count() }} Total Karyawan
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="employee-performance-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Total Tugas</th>
                        <th>Tugas Selesai</th>
                        <th>Nilai Kinerja</th>
                        <th>Kategori</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ $employee->name }}&background=random" class="rounded-circle me-2" width="40" height="40" alt="{{ $employee->name }}">
                                    <div>
                                        <div class="fw-semibold">{{ $employee->name }}</div>
                                        <small class="text-muted">{{ $employee->division->name ?? 'Tanpa Divisi' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-tasks me-1"></i>
                                    {{ $employee->total_assignments }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $employee->completed_assignments }}
                                </span>
                            </td>
                            <td>
                                @if ($employee->performance_score)
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $percentage = ($employee->performance_score / 4) * 100;
                                            $colorClass = 'bg-danger';

                                            if ($employee->performance_score >= 3.7) {
                                                $colorClass = 'bg-success';
                                            } elseif ($employee->performance_score >= 3) {
                                                $colorClass = 'bg-info';
                                            } elseif ($employee->performance_score >= 2.5) {
                                                $colorClass = 'bg-primary';
                                            } elseif ($employee->performance_score >= 2) {
                                                $colorClass = 'bg-warning';
                                            }
                                        @endphp

                                        <div class="progress-bar {{ $colorClass }}" role="progressbar"
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $employee->performance_score }}"
                                            aria-valuemin="0" aria-valuemax="4">
                                            {{ $employee->performance_score }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Belum ada penilaian</span>
                                @endif
                            </td>
                            <td>
                                @if ($employee->performance_score)
                                    @php
                                        $badgeClass = 'bg-danger';

                                        if ($employee->performance_score >= 3.7) {
                                            $badgeClass = 'bg-success';
                                        } elseif ($employee->performance_score >= 3) {
                                            $badgeClass = 'bg-info';
                                        } elseif ($employee->performance_score >= 2.5) {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($employee->performance_score >= 2) {
                                            $badgeClass = 'bg-warning';
                                        }
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $employee->performance_category }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('head_division.performances.show', $employee->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>

                                    @if ($employee->performance_score >= 3 && !$employee->has_pending_promotion)
                                        <a href="{{ route('head_division.performances.propose_promotion', $employee->id) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-award me-1"></i> Ajukan Promosi
                                        </a>
                                    @elseif ($employee->has_pending_promotion)
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-clock me-1"></i> Promosi Diajukan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Tidak ada data karyawan yang tersedia.</p>
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
        $('#employee-performance-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
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
                // Customize search input
                $('.dataTables_filter input').attr('placeholder', 'Cari karyawan...');
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