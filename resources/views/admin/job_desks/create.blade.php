@extends('layouts.admin')

@section('title', 'Buat Tugas Baru')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* Responsive Header */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .page-header .btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .page-header h1 {
            font-size: 1.25rem;
        }
    }

    /* Form Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem;
        }

        textarea.form-control {
            font-size: 0.875rem;
        }
    }

    /* Button Group Responsive */
    @media (max-width: 576px) {
        .button-group {
            flex-direction: column-reverse !important;
            gap: 0.5rem;
        }

        .button-group .btn {
            width: 100%;
        }
    }

    /* Select2 Responsive */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    @media (max-width: 768px) {
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Card Header Responsive */
    @media (max-width: 576px) {
        .card-header h6 {
            font-size: 0.875rem;
        }
    }

    /* Help Text */
    @media (max-width: 576px) {
        .form-text {
            font-size: 0.75rem;
        }
    }

    /* Custom styling for better UX */
    .form-label {
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #5e72e4;
        box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
    }

    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    /* Required field indicator */
    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Buat Tugas Baru</h1>
            <p class="text-secondary mb-0">Tambahkan tugas baru untuk karyawan di divisi Anda</p>
        </div>
        <a href="{{ route('admin.manage_job_desks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Kartu Form -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-clipboard-list me-2"></i>
                Informasi Tugas
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.manage_job_desks.store') }}" id="jobDeskForm">
                @csrf

                <!-- Judul -->
                <div class="mb-3">
                    <label for="title" class="form-label required">Judul Tugas</label>
                    <input type="text"
                           class="form-control @error('title') is-invalid @enderror"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           placeholder="Masukkan judul tugas"
                           required>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Berikan judul yang jelas dan deskriptif
                    </small>
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="description" class="form-label required">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="5"
                              placeholder="Jelaskan detail tugas yang harus dikerjakan..."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Jelaskan tugas secara detail agar karyawan memahami dengan baik
                    </small>
                </div>

                <!-- Tenggat Waktu -->
                <div class="mb-3">
                    <label for="deadline" class="form-label required">Tenggat Waktu</label>
                    <input type="date"
                           class="form-control @error('deadline') is-invalid @enderror"
                           id="deadline"
                           name="deadline"
                           value="{{ old('deadline') }}"
                           min="{{ date('Y-m-d') }}"
                           required>
                    @error('deadline')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Pilih tanggal deadline untuk tugas ini
                    </small>
                </div>

                <!-- Tugaskan ke Karyawan -->
                <div class="mb-4">
                    <label for="employees" class="form-label required">Tugaskan ke Karyawan</label>
                    <select class="form-select @error('employees') is-invalid @enderror"
                            id="employees"
                            name="employees[]"
                            multiple
                            required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employees', [])) ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->division->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('employees')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-users me-1"></i>
                        Pilih satu atau lebih karyawan untuk tugas ini
                    </small>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2 justify-content-end button-group">
                    <a href="{{ route('admin.manage_job_desks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (diperlukan untuk Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk dropdown karyawan
        $('#employees').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih karyawan...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Tidak ada karyawan yang ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });

        // Set minimum date untuk deadline (hari ini)
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('deadline').setAttribute('min', today);

        // Form validation
        const form = document.getElementById('jobDeskForm');

        form.addEventListener('submit', function(event) {
            const selectedEmployees = $('#employees').val();

            if (!selectedEmployees || selectedEmployees.length === 0) {
                event.preventDefault();
                event.stopPropagation();

                // Show error message
                const employeeSelect = document.getElementById('employees');
                employeeSelect.classList.add('is-invalid');

                // Create or update error message
                let errorDiv = employeeSelect.parentElement.querySelector('.invalid-feedback');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    employeeSelect.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Silakan pilih minimal satu karyawan';
                errorDiv.style.display = 'block';

                return false;
            }

            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        });

        // Remove invalid state when user selects employees
        $('#employees').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.invalid-feedback').hide();
            }
        });

        // Auto-resize textarea
        const textarea = document.getElementById('description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>
@endpush
@endsection
