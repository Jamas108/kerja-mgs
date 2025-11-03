<!-- resources/views/admin/users/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Pengguna</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error!</strong> Please check the form for errors.
                @if ($errors->has('general'))
                    <p>{{ $errors->first('general') }}</p>
                @endif
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Masukan Nama" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="Masukan Email" required>
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
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Masukan Password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Masukan Ulang Password" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Peran</label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id"
                                required>
                                <option value="">Pilih Peran</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" data-role-name="{{ strtolower($role->name) }}"
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="division-container">
                            <label for="division_id" class="form-label">Divisi</label>
                            <select class="form-select @error('division_id') is-invalid @enderror" id="division_id"
                                name="division_id">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan
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
            // Function to handle division field visibility
            function handleDivisionVisibility() {
                const selectedOption = $('#role_id option:selected');
                const roleName = selectedOption.data('role-name') || '';

                console.log('Selected role:', roleName); // Debug log

                if (roleName === 'karyawan' || roleName === 'kepala divisi') {
                    $('#division_id').prop('required', true);
                    $('#division-container').show();
                    $('.division-note').show();
                } else {
                    $('#division_id').prop('required', false);
                    if (roleName) { // Only hide if a valid role is selected
                        $('#division-container').hide();
                        $('.division-note').hide();
                        // Reset the division value when it's not required
                        $('#division_id').val('');
                    } else {
                        // If no role is selected, still show the division field
                        $('#division-container').show();
                        $('.division-note').show();
                    }
                }
            }

            // Handle role change
            $('#role_id').change(handleDivisionVisibility);

            // Initialize on page load
            handleDivisionVisibility();

            // Form validation before submit
            $('#createUserForm').on('submit', function(e) {
                const selectedRole = $('#role_id option:selected').data('role-name');
                const divisionId = $('#division_id').val();

                if ((selectedRole === 'karyawan' || selectedRole === 'kepala divisi') && !divisionId) {
                    e.preventDefault();
                    alert('Division is required for employees and division heads.');
                    $('#division_id').focus();
                    return false;
                }

                // Password validation
                const password = $('#password').val();
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long.');
                    $('#password').focus();
                    return false;
                }

                // Password confirmation match
                if (password !== $('#password_confirmation').val()) {
                    e.preventDefault();
                    alert('Password and confirmation do not match.');
                    $('#password_confirmation').focus();
                    return false;
                }

                // All validations passed
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            });
        });
    </script>
@endpush