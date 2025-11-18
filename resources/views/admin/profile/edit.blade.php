<!-- resources/views/admin/profile/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Edit Profil</h1>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-sm-0">
            <a href="{{ route('profile.index') }}" class="btn btn-secondary btn-sm btn-md-md">
                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Kembali</span>
            </a>
        </div>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 g-md-4">
            <!-- Photo Upload Card -->
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
                    </div>
                    <div class="card-body p-3 p-md-4 text-center">
                        <div class="mb-4">
                            <div id="imagePreview" class="mb-3">
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

                            <div class="mb-3">
                                <label for="photo" class="btn btn-primary btn-sm">
                                    <i class="fas fa-camera me-1"></i> Pilih Foto
                                </label>
                                <input type="file"
                                       class="d-none @error('photo') is-invalid @enderror"
                                       id="photo"
                                       name="photo"
                                       accept="image/*"
                                       onchange="previewImage(event)">
                                @error('photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($user->photo)
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmRemovePhoto()">
                                    <i class="fas fa-trash me-1"></i> Hapus Foto
                                </button>
                            @endif
                        </div>

                        <div class="alert alert-info text-start small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Info:</strong> Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="col-lg-8">
                <div class="card shadow mb-3 mb-md-4">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi</h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i> Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i> Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i> Peran
                                </label>
                                <input type="text"
                                       class="form-control"
                                       value="Administrator"
                                       disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i> Bergabung Sejak
                                </label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $user->created_at->format('d F Y') }}"
                                       disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="card shadow">
                    <div class="card-header py-2 py-md-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="alert alert-warning small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Kosongkan jika tidak ingin mengubah password. Password minimal 8 karakter.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Password Saat Ini
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password"
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
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key me-1"></i> Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('new_password') is-invalid @enderror"
                                           id="new_password"
                                           name="new_password"
                                           placeholder="Masukkan password baru">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                        <i class="fas fa-eye" id="new_password_icon"></i>
                                    </button>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i> Konfirmasi Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="new_password_confirmation"
                                           name="new_password_confirmation"
                                           placeholder="Konfirmasi password baru">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                        <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex gap-2 justify-content-end mt-3 mt-md-4">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Preview image before upload
    function previewImage(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('imagePreview');

        if (file) {
            // Validasi tipe file
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Format Tidak Valid: Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan!');
                event.target.value = '';
                return;
            }

            // Validasi ukuran file (max 2MB)
            if (file.size > 2048000) {
                alert('File Terlalu Besar: Ukuran file maksimal 2MB!');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--primary-light);" alt="Preview">`;
            }
            reader.readAsDataURL(file);
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

    // Confirm remove photo
    function confirmRemovePhoto() {
        Swal.fire({
            title: 'Hapus Foto Profil?',
            text: "Foto profil Anda akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("profile.photo.remove") }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Show success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush
@endsection
