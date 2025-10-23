@extends('layouts.head_division')

@section('title', 'Edit Job Desk')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Job Desk: {{ $jobDesk->title }}</h1>
        <a href="{{ route('head_division.job_desks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Job Desks
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Job Desk Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('head_division.job_desks.update', $jobDesk) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $jobDesk->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $jobDesk->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deadline" class="form-label">Deadline</label>
                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', $jobDesk->deadline->format('Y-m-d')) }}" required>
                    @error('deadline')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="employees" class="form-label">Assign to Employees</label>
                    <select multiple class="form-select @error('employees') is-invalid @enderror" id="employees" name="employees[]" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employees', $assignedEmployees)) ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple employees</small>
                    @error('employees')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Job Desk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection