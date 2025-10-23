@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Role Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <small class="form-text text-muted">The role name will be used for access control. Examples: admin, direktur, kepala divisi, karyawan</small>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="alert alert-info mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Role Information</h5>
                            <p class="mb-0">Creating a new role will allow you to assign it to users. The system currently recognizes these roles:</p>
                            <ul class="mt-2 mb-0">
                                <li><strong>admin</strong> - Full system access</li>
                                <li><strong>direktur</strong> - Can review and finalize task assessments</li>
                                <li><strong>kepala divisi</strong> - Can create tasks and do initial reviews</li>
                                <li><strong>karyawan</strong> - Can complete assigned tasks</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
