@extends('layouts.admin')

@section('title', 'Create Division')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Division</h1>
        <a href="{{ route('admin.divisions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Divisions
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Division Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.divisions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Division Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <div class="form-text">Enter a unique name for the division (e.g., IT, Marketing, Finance, HR)</div>
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
                            <h5 class="alert-heading">Division Setup Information</h5>
                            <p class="mb-0">After creating a division, you'll need to:</p>
                            <ol class="mt-2 mb-0">
                                <li>Assign a Kepala Divisi (Division Head)</li>
                                <li>Add employees to the division</li>
                                <li>The Division Head can then create and assign tasks to employees in this division</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Division
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
