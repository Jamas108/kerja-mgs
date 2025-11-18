@extends('layouts.admin')

@section('title', 'Edit Division')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Division</h1>
        <div>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Divisions
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Division Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.divisions.update', $division) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Division Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $division->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Division
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection