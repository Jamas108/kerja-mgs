@extends('layouts.director')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-wrap">
                <i class="fas fa-user-circle"></i>
            </div>
            <div>
                <h3 class="mb-0">Profil Saya</h3>
                <p class="text-muted mb-0 small">Kelola informasi profil Anda</p>
            </div>
        </div>
        <a href="{{ route('director.director_profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i>Edit Profil
        </a>
    </div>

    <!-- Alert -->
    @if(session('success') || session('error'))
        <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-{{ session('success') ? 'check' : 'exclamation' }}-circle me-2"></i>
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 g-md-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0  shadow-sm">
                <div class="card-body  text-center p-4">
                    <div class="avatar mb-3">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                    <p class="text-muted mb-2 small">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ $user->role->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Informasi Personal</h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <div>
                                    <div class="info-lbl">Nama Lengkap</div>
                                    <div class="info-val">{{ $user->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="far fa-envelope"></i>
                                <div>
                                    <div class="info-lbl">Email</div>
                                    <div class="info-val">{{ $user->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <div class="info-lbl">Divisi</div>
                                    <div class="info-val">{{ $user->division->name ?? 'Tidak Ada' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-user-tag"></i>
                                <div>
                                    <div class="info-lbl">Role</div>
                                    <div class="info-val">{{ $user->role->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="far fa-calendar-plus"></i>
                                <div>
                                    <div class="info-lbl">Bergabung Sejak</div>
                                    <div class="info-val">{{ $user->created_at->format('d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="far fa-clock"></i>
                                <div>
                                    <div class="info-lbl">Terakhir Diperbarui</div>
                                    <div class="info-val">{{ $user->updated_at->format('d F Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .icon-wrap {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .card {
        border-radius: 12px;
    }

    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin: 0 auto;
    }

    .stat-val {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
    }

    .stat-lbl {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    .info-item, .perf-item {
        display: flex;
        gap: 0.75rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 10px;
        align-items: center;
    }

    .info-item i {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #667eea15, #764ba215);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        flex-shrink: 0;
    }

    .info-lbl, .perf-lbl {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 500;
    }

    .info-val {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 600;
        word-break: break-word;
    }

    .perf-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .perf-val {
        font-size: 1.1rem;
        color: #1f2937;
        font-weight: 700;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    @media (min-width: 768px) {
        .icon-wrap {
            width: 60px;
            height: 60px;
            font-size: 28px;
        }
        .avatar {
            width: 120px;
            height: 120px;
            font-size: 36px;
        }
    }
</style>
@endpush
@endsection