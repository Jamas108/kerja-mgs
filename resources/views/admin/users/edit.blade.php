<!-- resources/views/admin/users/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengguna: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Lihat Detail
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kosongkan untuk mempertahankan password">
                        <small class="form-text text-muted">Kosongkan untuk mempertahankan password saat ini</small>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">Peran</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                            <option value="">Pilih Peran</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="division_id" class="form-label">Divisi</label>
                        <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id">
                            <option value="">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Wajib untuk Karyawan dan Kepala Divisi</small>
                        @error('division_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle division field visibility based on role
        $('#role_id').change(function() {
            const selectedText = $(this).find('option:selected').text().toLowerCase();
            if (selectedText === 'karyawan' || selectedText === 'kepala divisi') {
                $('#division_id').prop('required', true);
                $('#division_id').closest('.col-md-6').show();
            } else {
                $('#division_id').prop('required', false);
                $('#division_id').closest('.col-md-6').hide();
            }
        });

        // Trigger change on page load
        $('#role_id').trigger('change');
    });
</script>
@endpush