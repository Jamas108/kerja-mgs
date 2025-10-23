@extends('layouts.head_division')

@section('title', 'Kelola Tugas Karyawan')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Kelola Tugas Karyawan</h2>
        <p class="text-secondary mb-0">Kelola dan pantau semua tugas dalam divisi Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('head_division.job_desks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Tugas Baru
        </a>
    </div>
</div>

<!-- Job Desks Card -->
<div class="card h-100 mb-4">
    <div class="card-header">
        <h5 class="card-header-title">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>
            Semua Tugas
        </h5>
        <div class="card-header-actions">
            <span class="badge bg-primary-light text-primary py-2 px-3">
                <i class="fas fa-tasks me-1"></i>
                {{$jobDesks->count()}} Total Tugas
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Job Desk Tabs -->
        <ul class="nav nav-tabs px-4 pt-3" id="jobDeskTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                    <i class="fas fa-list me-2"></i>Semua
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inprogress-tab" data-bs-toggle="tab" data-bs-target="#inprogress" type="button" role="tab" aria-controls="inprogress" aria-selected="false">
                    <i class="fas fa-spinner me-2"></i>Dalam Proses
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                    <i class="fas fa-check-circle me-2"></i>Selesai
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notstarted-tab" data-bs-toggle="tab" data-bs-target="#notstarted" type="button" role="tab" aria-controls="notstarted" aria-selected="false">
                    <i class="fas fa-hourglass-start me-2"></i>Belum Dimulai
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4" id="jobDeskTabsContent">
            <!-- All Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="table-responsive">
                    <table class="table table-hover datatable-table" id="all-tasks-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tenggat Waktu</th>
                                <th>Ditugaskan Kepada</th>
                                <th>Status</th>
                                <th class="text-center no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($jobDesks->count() > 0)
                                @foreach($jobDesks as $jobDesk)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                    </td>
                                    <td>{{ Str::limit($jobDesk->description, 50) }}</td>
                                    <td>
                                        <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $jobDesk->deadline->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($jobDesk->assignments->count() > 0)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($jobDesk->assignments as $assignment)
                                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                                        <div class="user-avatar" style="width: auto; height: 30px; font-size: 12px;">
                                                            {{ $assignment->employee->name}}
                                                        </div>
                                                        {!! $assignment->status_badge !!}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Belum Ditugaskan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                            <span class="badge bg-success-light text-success">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        @elseif($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                            <span class="badge bg-primary-light text-primary">
                                                <i class="fas fa-spinner me-1"></i>Dalam Proses
                                            </span>
                                        @else
                                            <span class="badge bg-warning-light text-warning">
                                                <i class="fas fa-hourglass-start me-1"></i>Belum Dimulai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <div class="dropdown">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $jobDesk->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-eye me-2"></i>Lihat Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-user-plus me-2"></i>Tambah Penugasan
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('head_division.job_desks.destroy', $jobDesk) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus job desk ini?')">
                                                                <i class="fas fa-trash me-2"></i>Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Belum ada tugas yang dibuat. Klik "Tambah Job Desk Baru" untuk memulai.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- In Progress Tab -->
            <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">
                <div class="table-responsive">
                    <table class="table table-hover datatable-table" id="inprogress-tasks-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tenggat Waktu</th>
                                <th class="text-center">Ditugaskan Kepada</th>
                                <th>Status</th>
                                <th class="text-center no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $inProgressFound = false; @endphp
                            @foreach($jobDesks as $jobDesk)
                                @if($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                    @php $inProgressFound = true; @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director']) as $assignment)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar" style="width: auto; height: 28px; font-size: 12px;">
                                                            {{$assignment->employee->name}}
                                                        </div>
                                                        {!! $assignment->status_badge !!}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-light text-primary">
                                                <i class="fas fa-spinner me-1"></i>Dalam Proses
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$inProgressFound)
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Tidak ada job desk yang sedang dalam proses saat ini.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <div class="table-responsive">
                    <table class="table table-hover datatable-table" id="completed-tasks-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tenggat Waktu</th>
                                <th>Ditugaskan Kepada</th>
                                <th>Status</th>
                                <th class="text-end no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $completedFound = false; @endphp
                            @foreach($jobDesks as $jobDesk)
                                @if($jobDesk->assignments->where('status', 'final')->count() == $jobDesk->assignments->count() && $jobDesk->assignments->count() > 0)
                                    @php $completedFound = true; @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-success-light text-success">
                                                <i class="far fa-calendar-check me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($jobDesk->assignments as $assignment)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar" style="width: auto; height: 28px; font-size: 12px;">
                                                            {{ $assignment->employee->name }}
                                                        </div>
                                                        {!! $assignment->status_badge !!}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-light text-success">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$completedFound)
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Belum ada job desk yang telah selesai.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Not Started Tab -->
            <div class="tab-pane fade" id="notstarted" role="tabpanel" aria-labelledby="notstarted-tab">
                <div class="table-responsive">
                    <table class="table table-hover datatable-table" id="notstarted-tasks-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tenggat Waktu</th>
                                <th>Status</th>
                                <th class="text-end no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $notStartedFound = false; @endphp
                            @foreach($jobDesks as $jobDesk)
                                @if($jobDesk->assignments->count() == 0 || ($jobDesk->assignments->whereNotIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director', 'final'])->count() == $jobDesk->assignments->count()))
                                    @php $notStartedFound = true; @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td>{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning-light text-warning">
                                                <i class="fas fa-hourglass-start me-1"></i>Belum Dimulai
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-sm btn-success">
                                                    <i class="fas fa-user-plus me-1"></i> Tugaskan
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$notStartedFound)
                                <tr>
                                    <td colspan="5">
                                        <div class="alert alert-success d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-check-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Semua job desk telah ditugaskan. Kerja bagus!</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
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
        // Initialize DataTables for all tables with the datatable-table class
        $('.datatable-table').each(function() {
            $(this).DataTable({
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
                    $('.dataTables_filter input').attr('placeholder', 'Cari tugas...');
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_filter label').addClass('mb-0');

                    // Customize length menu
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });
        });

        // Handle tab changes - redraw DataTables when a tab is shown
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection