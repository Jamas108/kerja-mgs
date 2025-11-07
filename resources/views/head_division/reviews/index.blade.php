@extends('layouts.head_division')

@section('title', 'Ulasan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ulasan Tugas</h1>
    </div>

    <!-- Card Ulasan Menunggu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ulasan Menunggu</h6>
        </div>
        <div class="card-body">
            @if($pendingReviews->count() > 0)
                <!-- Tampilan Desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" id="pendingReviewsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Karyawan</th>
                                <th>Selesai Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReviews as $assignment)
                            <tr>
                                <td>{{ $assignment->jobDesk->title }}</td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->completed_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('head_division.reviews.show', $assignment) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Ulas
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile -->
                <div class="d-md-none">
                    @foreach($pendingReviews as $assignment)
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-2">{{ $assignment->jobDesk->title }}</h6>
                            <div class="mb-2">
                                <small class="text-muted">Karyawan:</small>
                                <p class="mb-1">{{ $assignment->employee->name }}</p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Selesai Pada:</small>
                                <p class="mb-0">{{ $assignment->completed_at->format('d M Y H:i') }}</p>
                            </div>
                            <a href="{{ route('head_division.reviews.show', $assignment) }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> Ulas
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Tidak ada ulasan yang menunggu saat ini.
                </div>
            @endif
        </div>
    </div>

    <!-- Card Ditolak oleh Direktur -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Ditolak oleh Direktur</h6>
        </div>
        <div class="card-body">
            @if($rejectedByDirector->count() > 0)
                <!-- Tampilan Desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" id="rejectedTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Karyawan</th>
                                <th>Penilaian Anda</th>
                                <th>Catatan Direktur</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedByDirector as $assignment)
                            <tr>
                                <td>{{ $assignment->jobDesk->title }}</td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->kadiv_rating }} / 4</td>
                                <td>{{ $assignment->director_notes }}</td>
                                <td>{!! $assignment->status_badge !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile -->
                <div class="d-md-none">
                    @foreach($rejectedByDirector as $assignment)
                    <div class="card mb-3 border-left-danger">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-danger mb-2">{{ $assignment->jobDesk->title }}</h6>
                            <div class="mb-2">
                                <small class="text-muted">Karyawan:</small>
                                <p class="mb-1">{{ $assignment->employee->name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Penilaian Anda:</small>
                                <p class="mb-1"><strong>{{ $assignment->kadiv_rating }} / 4</strong></p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Catatan Direktur:</small>
                                <p class="mb-1">{{ $assignment->director_notes }}</p>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted">Status:</small>
                                <div class="mt-1">{!! $assignment->status_badge !!}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Tidak ada tugas yang ditolak oleh direktur.
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styling untuk card mobile */
    @media (max-width: 767.98px) {
        .card-body h6 {
            font-size: 0.95rem;
        }

        .card-body small {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-body p {
            font-size: 0.875rem;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
    }
</style>
@endpush
@endsection