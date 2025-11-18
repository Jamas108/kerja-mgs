{{-- resources/views/profile/director.blade.php --}}
@extends('layouts.director')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Profil Saya</h1>
            <p class="text-muted mb-0">Kelola informasi profil dan keamanan akun Anda</p>
        </div>
    </div>

    <div class="row">
        <!-- Profile Overview Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="user-avatar mx-auto" style="width: 100px; height: 100px; font-size: 42px; border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ auth()->user()->name }}</h4>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>

                    <div class="badge bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px 16px; font-size: 13px;">
                        <i class="fas fa-crown me-1"></i>
                        Direktur
                    </div>
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title mb-3 fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informasi Akun
                    </h6>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Tanggal Bergabung</small>
                        <strong class="text-dark">{{ auth()->user()->created_at->format('d M Y') }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Terakhir Diperbarui</small>
                        <strong class="text-dark">{{ auth()->user()->updated_at->format('d M Y H:i') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <h6 class="mb-3 fw-bold">
                        <i class="fas fa-chart-pie me-2"></i>
                        Statistik Director
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-clipboard-check me-2"></i>Pending Reviews</span>
                        <strong>{{ \App\Models\EmployeeJobDesk::where('status', 'reviewed_by_kadiv')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-award me-2"></i>Pending Promosi</span>
                        <strong>{{ \App\Models\PromotionRequest::where('status', 'pending')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users me-2"></i>Total Karyawan</span>
                        <strong>{{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'karyawan'); })->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="col-lg-8">
            <!-- Edit Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Edit Profil
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control border-start-0 @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', auth()->user()->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', auth()->user()->email) }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Peran</label>
                                <input type="text"
                                       class="form-control bg-light"
                                       value="Direktur"
                                       disabled>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
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
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Ubah Password
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 mb-4" style="background-color: rgba(14, 165, 233, 0.1);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips Keamanan:</strong> Gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol. Minimal 8 karakter.
                    </div>

                    <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-key text-muted"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control border-start-0 border-end-0 @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password"
                                           required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye" id="current_password_icon"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="password" class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password_icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control border-start-0 border-end-0"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">
                                <i class="fas fa-undo me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-warning text-white">
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
@endsection

@push('scripts')
<script>
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

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;

    if (password !== confirmation) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        return false;
    }

    if (password.length < 8) {
        e.preventDefault();
        alert('Password minimal 8 karakter!');
        return false;
    }
});
</script>
@endpush