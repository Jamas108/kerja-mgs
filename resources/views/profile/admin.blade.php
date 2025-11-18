{{-- resources/views/profile/admin.blade.php --}}
@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-4" style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">Profil Saya</h1>
                    <p class="text-muted mb-0">Kelola informasi profil dan keamanan akun Anda</p>
                </div>
                <div>
                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.875rem;">
                        <i class="fas fa-user-shield me-1"></i>
                        {{ ucfirst(auth()->user()->role->name ?? 'No Role') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Centered Container with Max Width -->
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">
            <div class="row g-4">
                <!-- Left Column - Profile Card -->
                <div class="col-xl-4 col-lg-5">
                    <!-- Profile Overview Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center py-4">
                            <!-- Photo Upload Section -->
                            <div class="position-relative d-inline-block mb-3">
                                @if(auth()->user()->photo)
                                    <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                         alt="Profile Photo"
                                         class="rounded-circle border border-3 border-white shadow"
                                         style="width: 120px; height: 120px; object-fit: cover;"
                                         id="profilePhotoPreview">
                                @else
                                    <div class="rounded-circle border border-3 border-white shadow d-flex align-items-center justify-content-center"
                                         style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 48px; color: white; font-weight: 600;"
                                         id="profilePhotoPreview">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif

                                <!-- Upload Button Overlay -->
                                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                                    @csrf
                                    <label for="photoInput" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle shadow"
                                           style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                                           data-bs-toggle="tooltip"
                                           title="Ubah Foto">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    <input type="file"
                                           id="photoInput"
                                           name="photo"
                                           accept="image/jpeg,image/png,image/jpg,image/gif"
                                           class="d-none"
                                           onchange="handlePhotoChange(this)">
                                </form>
                            </div>

                            <!-- Delete Photo Button (if photo exists) -->
                            @if(auth()->user()->photo)
                            <div class="mb-3">
                                <form action="{{ route('profile.photo.delete') }}" method="POST" id="deletePhotoForm" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeletePhoto()">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        Hapus Foto
                                    </button>
                                </form>
                            </div>
                            @endif

                            <h4 class="mb-1 fw-bold">{{ auth()->user()->name }}</h4>
                            <p class="text-muted mb-3 small">{{ auth()->user()->email }}</p>

                            @if(auth()->user()->division)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted d-block mb-1">Divisi</small>
                                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.875rem;">
                                    <i class="fas fa-building me-1"></i>
                                    {{ auth()->user()->division->name }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Info Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3 fw-bold d-flex align-items-center">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi Akun
                            </h6>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Tanggal Bergabung
                                </small>
                                <strong class="text-dark">{{ auth()->user()->created_at->format('d M Y') }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-clock me-1"></i>
                                    Terakhir Diperbarui
                                </small>
                                <strong class="text-dark">{{ auth()->user()->updated_at->format('d M Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white">
                            <h6 class="mb-3 fw-bold">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistik Cepat
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                                <span><i class="fas fa-users me-2"></i>Total Users</span>
                                <strong class="fs-5">{{ \App\Models\User::count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                                <span><i class="fas fa-building me-2"></i>Total Divisi</span>
                                <strong class="fs-5">{{ \App\Models\Division::count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user-tag me-2"></i>Total Roles</span>
                                <strong class="fs-5">{{ \App\Models\Role::count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Forms -->
                <div class="col-xl-8 col-lg-7">
                    <!-- Edit Profile Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-user-edit text-primary me-2"></i>
                                Edit Profil
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold">
                                            <i class="fas fa-user text-muted me-1"></i>
                                            Nama Lengkap
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', auth()->user()->name) }}"
                                               required
                                               placeholder="Masukkan nama lengkap">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            <i class="fas fa-envelope text-muted me-1"></i>
                                            Email
                                        </label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email', auth()->user()->email) }}"
                                               required
                                               placeholder="email@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user-shield text-muted me-1"></i>
                                            Peran
                                        </label>
                                        <input type="text"
                                               class="form-control bg-light"
                                               value="{{ ucfirst(auth()->user()->role->name ?? 'No Role') }}"
                                               disabled>
                                    </div>

                                    @if(auth()->user()->division)
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-building text-muted me-1"></i>
                                            Divisi
                                        </label>
                                        <input type="text"
                                               class="form-control bg-light"
                                               value="{{ auth()->user()->division->name }}"
                                               disabled>
                                    </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light" onclick="window.location.reload()">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-lock text-warning me-2"></i>
                                Ubah Password
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 mb-4 d-flex align-items-start" style="background-color: rgba(14, 165, 233, 0.1);">
                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                <div class="small">
                                    <strong>Tips Keamanan:</strong> Gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol. Minimal 8 karakter.
                                </div>
                            </div>

                            <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="current_password" class="form-label fw-semibold">
                                            <i class="fas fa-key text-muted me-1"></i>
                                            Password Saat Ini
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control @error('current_password') is-invalid @enderror"
                                                   id="current_password"
                                                   name="current_password"
                                                   required
                                                   placeholder="Masukkan password saat ini">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="fas fa-eye" id="current_password_icon"></i>
                                            </button>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold">
                                            <i class="fas fa-lock text-muted me-1"></i>
                                            Password Baru
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   id="password"
                                                   name="password"
                                                   required
                                                   placeholder="Minimal 8 karakter">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="password_icon"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold">
                                            <i class="fas fa-check-circle text-muted me-1"></i>
                                            Konfirmasi Password
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control"
                                                   id="password_confirmation"
                                                   name="password_confirmation"
                                                   required
                                                   placeholder="Ulangi password baru">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                                    <button type="reset" class="btn btn-light">
                                        <i class="fas fa-undo me-1"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-1"></i>
                                        Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom Styles for Profile Page */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .form-control:focus,
    .input-group .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5568d3 0%, #65408b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #de82e0 0%, #dc465b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
        color: white;
    }

    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .card-body {
            padding: 1.25rem !important;
        }
    }

    /* Photo upload hover effect */
    #profilePhotoPreview {
        transition: all 0.3s ease;
    }

    .position-relative:hover #profilePhotoPreview {
        opacity: 0.9;
    }
</style>
@endpush

@push('scripts')
<script>
// Handle photo change - SUPER SIMPLE VERSION
function handlePhotoChange(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file size (max 2MB)
        if (file.size > 2048 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimal ukuran file adalah 2MB',
                confirmButtonColor: '#667eea'
            });
            input.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Tipe File Salah',
                text: 'File harus berupa gambar (JPEG, PNG, JPG, atau GIF)',
                confirmButtonColor: '#667eea'
            });
            input.value = '';
            return;
        }

        // Preview image first
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePhotoPreview');
            preview.outerHTML = `<img src="${e.target.result}"
                                      alt="Profile Photo"
                                      class="rounded-circle border border-3 border-white shadow"
                                      style="width: 120px; height: 120px; object-fit: cover;"
                                      id="profilePhotoPreview">`;
        };
        reader.readAsDataURL(file);

        // Show loading and submit
        Swal.fire({
            title: 'Mengunggah Foto...',
            html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });

        // Submit form directly
        setTimeout(() => {
            document.getElementById('photoUploadForm').submit();
        }, 500);
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');

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

// Confirm delete photo
function confirmDeletePhoto() {
    Swal.fire({
        title: 'Hapus Foto Profil?',
        text: "Foto profil Anda akan dihapus permanen",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deletePhotoForm').submit();
        }
    });
}

// Form validation for password
const passwordForm = document.getElementById('passwordForm');
if (passwordForm) {
    passwordForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        if (password !== confirmation) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Password baru dan konfirmasi password tidak cocok!',
                confirmButtonColor: '#667eea'
            });
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Terlalu Pendek',
                text: 'Password minimal 8 karakter!',
                confirmButtonColor: '#667eea'
            });
            return false;
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush