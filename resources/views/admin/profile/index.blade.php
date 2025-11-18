<!-- resources/views/admin/profile/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Profil Saya</h1>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-sm-0">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm btn-md-md">
                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit Profil</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm btn-md-md">
                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Kembali</span>
            </a>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-3 mb-md-4 " style="height: 440px">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="mb-4">
                        @if($user->photo)
                            <img src="{{ Storage::url($user->photo) }}"
                                 alt="{{ $user->name }}"
                                 class="rounded-circle shadow-sm"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--primary-light);">
                        @else
                            <div class="rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%); border: 4px solid var(--primary-light);">
                                <span class="text-white" style="font-size: 60px; font-weight: 600;">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <h4 class="mb-1 font-weight-bold">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-danger px-3 py-2 mb-3">
                        <i class="fas fa-shield-alt me-1"></i> Administrator
                    </span>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-3 mb-md-4">
                <div class="card-header py-2 py-md-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi</h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-user me-2"></i>Nama Lengkap
                                </label>
                                <div class="h6 mb-0">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <div class="h6 mb-0">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-shield-alt me-2"></i>Peran
                                </label>
                                <span class="badge bg-danger px-3 py-2">Administrator</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-calendar-alt me-2"></i>Bergabung Sejak
                                </label>
                                <div class="h6 mb-0">{{ $user->created_at->format('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card shadow">
                <div class="card-header py-2 py-md-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keamanan Akun</h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">Password</h6>
                            <p class="text-muted small mb-0">Terakhir diubah: {{ $user->updated_at->format('d F Y') }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key me-1"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection