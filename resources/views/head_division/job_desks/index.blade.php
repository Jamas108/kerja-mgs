@extends('layouts.head_division')

@section('title', 'Kelola Tugas Karyawan')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Responsive Header */
    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .page-header .btn {
            width: 100%;
            font-size: 0.875rem;
        }
    }

    /* Responsive Tabs */
    @media (max-width: 768px) {
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .nav-tabs .nav-link {
            white-space: nowrap;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .nav-tabs .nav-link i {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .nav-tabs .nav-link {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
    }

    /* Responsive Table */
    @media (max-width: 992px) {
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

        .badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Mobile Table Cards */
    @media (max-width: 768px) {
        .datatable-table thead {
            display: none;
        }

        .datatable-table tbody tr {
            display: block;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .datatable-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border: none;
            white-space: normal;
        }

        .datatable-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
            flex: 0 0 40%;
            color: #6c757d;
        }

        .datatable-table tbody td:first-child {
            display: none; /* Hide No column on mobile */
        }

        .action-buttons {
            flex-direction: column !important;
            width: 100%;
            gap: 0.5rem !important;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .user-avatar {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Card Header Responsive */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .card-header-title {
            font-size: 1rem;
        }

        .card-header-actions {
            width: 100%;
        }
    }

    /* DataTables Controls Responsive */
    @media (max-width: 768px) {
        .dataTables_wrapper .d-flex {
            flex-direction: column;
            align-items: stretch !important;
        }

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
        }
    }

    /* Assigned Users Column */
    @media (max-width: 768px) {
        .user-list {
            flex-direction: column;
            gap: 0.25rem;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 2rem 1rem;
        text-align: center;
    }

    @media (max-width: 576px) {
        .empty-state {
            padding: 1.5rem 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Halaman -->
<div class="page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
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

<!-- Kartu Tugas -->
<div class="card h-100 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-header-title mb-0">
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
        <!-- Tab Tugas -->
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

        <!-- Konten Tab -->
        <div class="tab-content p-4" id="jobDeskTabsContent">
            <!-- Tab Semua -->
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
                                    <td data-label="No">{{$loop->iteration}}</td>
                                    <td data-label="Judul">
                                        <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                    </td>
                                    <td data-label="Deskripsi">{{ Str::limit($jobDesk->description, 50) }}</td>
                                    <td data-label="Tenggat Waktu">
                                        <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $jobDesk->deadline->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td data-label="Ditugaskan Kepada">
                                        @if($jobDesk->assignments->count() > 0)
                                            <div class="d-flex flex-column gap-1 user-list">
                                                @foreach($jobDesk->assignments as $assignment)
                                                    <div class="d-flex align-items-center gap-2">
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
                                    <td data-label="Status">
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
                                    <td data-label="Aksi">
                                        <div class="d-flex justify-content-end gap-2 action-buttons">
                                            <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye me-1"></i> 
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete('{{ $jobDesk->id }}', '{{ $jobDesk->title }}')"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Hapus tugas ini">
                                                <i class="fas fa-trash me-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Belum ada tugas yang dibuat. Klik "Tambah Tugas Baru" untuk memulai.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Dalam Proses -->
            <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">
                <div class="table-responsive">
                    <table class="table table-hover datatable-table" id="inprogress-tasks-table">
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
                            @php $inProgressFound = false; @endphp
                            @foreach($jobDesks as $jobDesk)
                                @if($jobDesk->assignments->whereIn('status', ['assigned', 'completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'rejected_kadiv', 'rejected_director'])->count() > 0)
                                    @php $inProgressFound = true; @endphp
                                    <tr>
                                        <td data-label="No">{{$loop->iteration}}</td>
                                        <td data-label="Judul">
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td data-label="Deskripsi">{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td data-label="Tenggat Waktu">
                                            <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td data-label="Ditugaskan Kepada">
                                            <div class="d-flex flex-column gap-1 user-list">
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
                                        <td data-label="Status">
                                            <span class="badge bg-primary-light text-primary">
                                                <i class="fas fa-spinner me-1"></i>Dalam Proses
                                            </span>
                                        </td>
                                        <td data-label="Aksi">
                                            <div class="d-flex justify-content-end gap-2 action-buttons">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete('{{ $jobDesk->id }}', '{{ $jobDesk->title }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Hapus tugas ini">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$inProgressFound)
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Tidak ada tugas yang sedang dalam proses saat ini.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Selesai -->
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
                                        <td data-label="No">{{$loop->iteration}}</td>
                                        <td data-label="Judul">
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td data-label="Deskripsi">{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td data-label="Tenggat Waktu">
                                            <span class="badge bg-success-light text-success">
                                                <i class="far fa-calendar-check me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td data-label="Ditugaskan Kepada">
                                            <div class="d-flex flex-column gap-1 user-list">
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
                                        <td data-label="Status">
                                            <span class="badge bg-success-light text-success">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        </td>
                                        <td data-label="Aksi">
                                            <div class="d-flex justify-content-end gap-2 action-buttons">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete('{{ $jobDesk->id }}', '{{ $jobDesk->title }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Hapus tugas ini">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$completedFound)
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-info-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Belum ada tugas yang telah selesai.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Belum Dimulai -->
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
                                        <td data-label="No">{{$loop->iteration}}</td>
                                        <td data-label="Judul">
                                            <span class="fw-semibold">{{ $jobDesk->title }}</span>
                                        </td>
                                        <td data-label="Deskripsi">{{ Str::limit($jobDesk->description, 50) }}</td>
                                        <td data-label="Tenggat Waktu">
                                            <span class="badge {{ \Carbon\Carbon::parse($jobDesk->deadline)->isPast() ? 'bg-danger-light text-danger' : 'bg-primary-light text-primary' }}">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $jobDesk->deadline->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge bg-warning-light text-warning">
                                                <i class="fas fa-hourglass-start me-1"></i>Belum Dimulai
                                            </span>
                                        </td>
                                        <td data-label="Aksi">
                                            <div class="d-flex justify-content-end gap-2 action-buttons">
                                                <a href="{{ route('head_division.job_desks.edit', $jobDesk) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-sm btn-success">
                                                    <i class="fas fa-user-plus me-1"></i> Tugaskan
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete('{{ $jobDesk->id }}', '{{ $jobDesk->title }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Hapus tugas ini">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$notStartedFound)
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-success d-flex align-items-center m-0" role="alert">
                                            <div class="alert-icon me-3">
                                                <i class="fas fa-check-circle fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">Semua tugas telah ditugaskan. Kerja bagus!</p>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                </div>
                <p>Apakah Anda yakin ingin menghapus tugas: <strong id="taskTitle"></strong>?</p>
                <p class="text-muted small">Semua data terkait tugas ini akan dihapus secara permanen, termasuk assignee dan progress yang telah dibuat.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus Tugas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (diperlukan untuk DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables untuk semua tabel dengan class datatable-table
        $('.datatable-table').each(function() {
            $(this).DataTable({
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
                    $('.dataTables_filter input').attr('placeholder', 'Cari tugas...');
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_filter label').addClass('mb-0');

                    // Kustomisasi menu panjang
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });
        });

        // Handle perubahan tab - redraw DataTables ketika tab ditampilkan
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Inisialisasi tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

    // Fungsi konfirmasi hapus
    function confirmDelete(jobDeskId, jobDeskTitle) {
        // Set judul tugas di modal
        document.getElementById('taskTitle').textContent = jobDeskTitle;

        // Set action form untuk menghapus
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = "{{ route('head_division.job_desks.index') }}" + "/" + jobDeskId;

        // Tampilkan modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    }
</script>
@endpush
@endsection