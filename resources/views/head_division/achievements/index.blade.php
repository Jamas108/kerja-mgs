@extends('layouts.head_division')

@section('title', 'Penghargaan Karyawan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-header-title">
                        <i class="fas fa-award text-primary me-2"></i>Daftar Penghargaan Karyawan
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Status Tab -->
                <div class="mb-4">
                    <ul class="nav nav-pills nav-justified flex-column flex-sm-row" id="certificatesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status', 'all') == 'all' ? 'active' : '' }}"
                               href="{{ route('head_division.achievements-member.index') }}">
                                Semua <span class="badge bg-primary ms-1">{{ $statusCounts['all'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}"
                               href="{{ route('head_division.achievements-member.index', ['status' => 'approved']) }}">
                                Disetujui <span class="badge bg-success ms-1">{{ $statusCounts['approved'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                               href="{{ route('head_division.achievements-member.index', ['status' => 'pending']) }}">
                                Menunggu <span class="badge bg-warning ms-1">{{ $statusCounts['pending'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}"
                               href="{{ route('head_division.achievements-member.index', ['status' => 'rejected']) }}">
                                Ditolak <span class="badge bg-danger ms-1">{{ $statusCounts['rejected'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                @if($achievements->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-certificate text-secondary" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Tidak Ada Penghargaan Ditemukan</h5>
                        <p class="text-muted">
                            Tidak ada penghargaan yang cocok dengan filter yang dipilih.
                            Coba filter berbeda atau ajukan penghargaan baru untuk anggota tim Anda.
                        </p>
                        <a href="{{ route('head_division.performances.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-users me-1"></i> Lihat Kinerja Karyawan
                        </a>
                    </div>
                @else
                    <div class="row">
                        @foreach($achievements as $achievement)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        <div class="certificate-preview bg-light text-center py-5">
                                            @if($achievement->status == 'approved' && $achievement->certificate_file)
                                                <i class="fas fa-certificate text-primary" style="font-size: 3rem;"></i>
                                            @elseif($achievement->status == 'pending')
                                                <i class="fas fa-hourglass-half text-warning" style="font-size: 3rem;"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                                            @endif
                                        </div>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            @if($achievement->status == 'approved')
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check me-1"></i>Disetujui
                                                </span>
                                            @elseif($achievement->status == 'pending')
                                                <span class="badge bg-warning rounded-pill">
                                                    <i class="fas fa-clock me-1"></i>Menunggu
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">
                                                    <i class="fas fa-times me-1"></i>Ditolak
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-primary-light text-primary rounded-circle">
                                                    {{ strtoupper(substr($achievement->employee->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="card-title mb-0">{{ $achievement->employee->name }}</h5>
                                                <small class="text-muted">{{ $achievement->employee->division->name ?? 'Tidak Ada Divisi' }}</small>
                                            </div>
                                        </div>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                Diminta: {{ $achievement->created_at->format('d M Y') }}
                                            </small>
                                        </p>
                                        @if($achievement->reviewed_at)
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-check me-1"></i>
                                                Ditinjau: {{ $achievement->reviewed_at->format('d M Y') }}
                                            </small>
                                        </p>
                                        @endif
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                Periode: {{ $achievement->period ?? 'N/A' }}
                                            </small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 d-flex flex-column flex-sm-row justify-content-between gap-2">
                                        <a href="{{ route('head_division.achievements-member.show', $achievement) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </a>
                                        @if($achievement->status == 'approved' && $achievement->certificate_file)
                                            <a href="{{ route('head_division.achievements-member.download', $achievement) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i> Unduh
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $achievements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .certificate-preview {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: var(--card-border-radius) var(--card-border-radius) 0 0;
    }

    .certificate-preview i {
        opacity: 0.5;
    }

    .card:hover .certificate-preview i {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .nav-pills .nav-link {
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        color: var(--secondary-color);
        background-color: var(--light-color);
        margin: 0 0.25rem;
    }

    .nav-pills .nav-link:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        color: white;
    }

    /* Responsivitas untuk tablet dan desktop kecil */
    @media (max-width: 992px) {
        .col-lg-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    /* Responsivitas untuk tablet */
    @media (max-width: 768px) {
        .nav-pills .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }
        .certificate-preview {
            height: 150px;
        }
        .certificate-preview i {
            font-size: 2.5rem;
        }
        .card-footer .btn {
            width: 100%;
        }
        .col-md-4 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    /* Responsivitas untuk ponsel */
    @media (max-width: 576px) {
        .nav-pills {
            flex-direction: column;
        }
        .nav-pills .nav-link {
            text-align: center;
        }
        .certificate-preview {
            height: 120px;
        }
        .certificate-preview i {
            font-size: 2rem;
        }
        .avatar-sm {
            width: 35px;
            height: 35px;
        }
        .card-title {
            font-size: 1rem;
        }
        .card-footer {
            padding: 0.75rem;
        }
        .col-sm-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endpush