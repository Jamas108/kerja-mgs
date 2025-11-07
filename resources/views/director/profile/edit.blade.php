@extends('layouts.director')

@section('title', 'Edit Profil')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Edit Profil</h3>
                            <p class="text-muted mb-0">Perbarui informasi profil Anda</p>
                        </div>
                    </div>
                    <a href="{{ route('director.director_profile.index') }}" class="btn btn-light shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-primary me-2"></i>
                            <h5 class="mb-0">Informasi Profil</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('director.director_profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Display -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Foto Profil</label>
                                <div class="avatar-display-wrapper">
                                    <div class="avatar-placeholder">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="avatar-info">
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Avatar ditampilkan berdasarkan inisial nama Anda
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" readonly
                                        required>
                                    <small class="text-muted">Email tidak dapat diubah</small>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Division (Read Only) -->
                                <div class="col-md-6">
                                    <label for="division" class="form-label fw-semibold">Divisi</label>
                                    <input type="text" class="form-control" id="division"
                                        value="{{ $user->division->name ?? 'Tidak Ada' }}" readonly>
                                    <small class="text-muted">Division ini tidak dapat diubah</small>
                                </div>

                                <!-- Role (Read Only) -->
                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-semibold">Peran</label>
                                    <input type="text" class="form-control" id="role"
                                        value="{{ $user->role->name ?? 'N/A' }}" readonly>
                                    <small class="text-muted">Peran ini tidak dapat diubah</small>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <a href="{{ route('director.director_profile.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock text-primary me-2"></i>
                            <h5 class="mb-0">Ubah Password</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('director.director_profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">
                                    Password Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-key me-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Icon Wrapper */
            .icon-wrapper {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                color: white;
            }

            /* Card */
            .card {
                border-radius: 16px;
            }

            /* Avatar Display */
            .avatar-display-wrapper {
                display: flex;
                align-items: center;
                gap: 2rem;
                padding: 1.5rem;
                background: #f9fafb;
                border-radius: 12px;
            }

            .avatar-placeholder {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                font-weight: 700;
                color: white;
                border: 4px solid #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                flex-shrink: 0;
            }

            .avatar-info {
                flex: 1;
            }

            /* Form Control */
            .form-control,
            .form-select {
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                padding: 0.625rem 1rem;
                transition: all 0.2s ease;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .form-label {
                color: #374151;
                margin-bottom: 0.5rem;
            }

            /* Buttons */
            .btn {
                font-weight: 600;
                border-radius: 8px;
                padding: 0.625rem 1.25rem;
                transition: all 0.3s ease;
            }

            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            .btn-light {
                font-weight: 600;
            }

            .btn-light:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .btn-outline-secondary {
                border: 2px solid #e5e7eb;
            }

            .btn-outline-secondary:hover {
                background: #f9fafb;
                border-color: #d1d5db;
            }

            /* Account Info */
            .account-info-item {
                padding-bottom: 1rem;
                border-bottom: 1px solid #e5e7eb;
            }

            .account-info-item:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            /* Badge */
            .badge {
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.35rem 0.65rem;
            }

            /* Alert */
            .alert {
                border-radius: 12px;
                border: none;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .icon-wrapper {
                    width: 50px;
                    height: 50px;
                    font-size: 24px;
                }

                .avatar-display-wrapper {
                    flex-direction: column;
                    text-align: center;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Toggle password visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = event.currentTarget.querySelector('i');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>
    @endpush
@endsection
