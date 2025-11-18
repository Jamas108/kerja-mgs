@extends('layouts.admin')

@section('title', 'Edit Assignment')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Assignment</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Lihat
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.reviews.update', $assignment) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Judul Tugas</label>
                                    <input type="text" class="form-control" value="{{ $assignment->jobDesk->title }}"
                                        readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label font-weight-bold">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        @foreach ($statusOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $assignment->status === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Karyawan</label>
                                    <input type="text" class="form-control" value="{{ $assignment->employee->name }}"
                                        readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Divisi</label>
                                    <input type="text" class="form-control"
                                        value="{{ $assignment->jobDesk->division->name }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evidence Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Bukti Penyelesaian</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="evidence_note" class="form-label font-weight-bold">Catatan Bukti</label>
                                <textarea class="form-control @error('evidence_note') is-invalid @enderror" id="evidence_note" name="evidence_note"
                                    rows="4" placeholder="Catatan bukti penyelesaian...">{{ old('evidence_note', $assignment->evidence_note) }}</textarea>
                                @error('evidence_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($assignment->evidence_file)
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">File Bukti Saat Ini</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="{{ asset('storage/' . $assignment->evidence_file) }}"
                                            class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> Lihat/Unduh
                                        </a>
                                        <small class="text-muted">{{ basename($assignment->evidence_file) }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Penilaian & Review</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Kadiv Review -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="font-weight-bold text-warning">Review Kepala Divisi</h6>

                                    <div class="mb-3">
                                        <label for="kadiv_rating" class="form-label">Rating (1-4)</label>
                                        <select class="form-control @error('kadiv_rating') is-invalid @enderror"
                                            id="kadiv_rating" name="kadiv_rating">
                                            <option value="">Belum dinilai</option>
                                            <option value="1"
                                                {{ old('kadiv_rating', $assignment->kadiv_rating) == 1 ? 'selected' : '' }}>
                                                1 - Buruk</option>
                                            <option value="2"
                                                {{ old('kadiv_rating', $assignment->kadiv_rating) == 2 ? 'selected' : '' }}>
                                                2 - Cukup</option>
                                            <option value="3"
                                                {{ old('kadiv_rating', $assignment->kadiv_rating) == 3 ? 'selected' : '' }}>
                                                3 - Baik</option>
                                            <option value="4"
                                                {{ old('kadiv_rating', $assignment->kadiv_rating) == 4 ? 'selected' : '' }}>
                                                4 - Sangat Baik</option>
                                        </select>
                                        @error('kadiv_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="kadiv_notes" class="form-label">Catatan</label>
                                        <textarea class="form-control @error('kadiv_notes') is-invalid @enderror" id="kadiv_notes" name="kadiv_notes"
                                            rows="3" placeholder="Catatan review kepala divisi...">{{ old('kadiv_notes', $assignment->kadiv_notes) }}</textarea>
                                        @error('kadiv_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($assignment->kadiv_reviewed_at)
                                        <small class="text-muted">
                                            Review pada: {{ $assignment->kadiv_reviewed_at->format('d M Y H:i') }}
                                        </small>
                                    @endif
                                </div>

                                <!-- Director Review -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="font-weight-bold text-primary">Review Direktur</h6>

                                    <div class="mb-3">
                                        <label for="director_rating" class="form-label">Rating (1-4)</label>
                                        <select class="form-control @error('director_rating') is-invalid @enderror"
                                            id="director_rating" name="director_rating">
                                            <option value="">Belum dinilai</option>
                                            <option value="1"
                                                {{ old('director_rating', $assignment->director_rating) == 1 ? 'selected' : '' }}>
                                                1 - Buruk</option>
                                            <option value="2"
                                                {{ old('director_rating', $assignment->director_rating) == 2 ? 'selected' : '' }}>
                                                2 - Cukup</option>
                                            <option value="3"
                                                {{ old('director_rating', $assignment->director_rating) == 3 ? 'selected' : '' }}>
                                                3 - Baik</option>
                                            <option value="4"
                                                {{ old('director_rating', $assignment->director_rating) == 4 ? 'selected' : '' }}>
                                                4 - Sangat Baik</option>
                                        </select>
                                        @error('director_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="director_notes" class="form-label">Catatan</label>
                                        <textarea class="form-control @error('director_notes') is-invalid @enderror" id="director_notes"
                                            name="director_notes" rows="3" placeholder="Catatan review direktur...">{{ old('director_notes', $assignment->director_notes) }}</textarea>
                                        @error('director_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($assignment->director_reviewed_at)
                                        <small class="text-muted">
                                            Review pada: {{ $assignment->director_reviewed_at->format('d M Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">Catatan Admin</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="admin_notes" class="form-label font-weight-bold">Catatan Internal</label>
                                <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="admin_notes" name="admin_notes"
                                    rows="4" placeholder="Catatan internal admin untuk assignment ini...">{{ old('admin_notes', $assignment->admin_notes) }}</textarea>
                                @error('admin_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Catatan ini hanya terlihat oleh admin dan tidak akan ditampilkan ke
                                    user lain.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Actions -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status Info -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <div class="mb-2">
                                    <strong>Status Saat Ini:</strong><br>
                                    {!! $assignment->status_badge !!}
                                </div>

                                <div class="mb-2">
                                    <strong>Dibuat:</strong><br>
                                    {{ $assignment->created_at->format('d M Y H:i') }}
                                </div>

                                @if ($assignment->completed_at)
                                    <div class="mb-2">
                                        <strong>Diselesaikan:</strong><br>
                                        {{ $assignment->completed_at->format('d M Y H:i') }}
                                    </div>
                                @endif

                                @if ($assignment->updated_by_admin)
                                    <div class="mb-2">
                                        <strong>Update Admin Terakhir:</strong><br>
                                        {{ $assignment->admin_updated_at ? $assignment->admin_updated_at->format('d M Y H:i') : '-' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Change Guide -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Panduan Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1"><strong>Assigned:</strong> Tugas baru diberikan</li>
                                    <li class="mb-1"><strong>In Progress:</strong> Sedang dikerjakan</li>
                                    <li class="mb-1"><strong>Completed:</strong> Selesai dikerjakan</li>
                                    <li class="mb-1"><strong>Review Kadiv:</strong> Menunggu review kepala divisi</li>
                                    <li class="mb-1"><strong>Review Direktur:</strong> Menunggu review direktur</li>
                                    <li class="mb-1"><strong>Rejected:</strong> Ditolak untuk revisi</li>
                                    <li class="mb-0"><strong>Final:</strong> Selesai dan disetujui</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Perubahan pada assignment ini akan mempengaruhi alur kerja. Pastikan
                        status yang dipilih sesuai dengan kondisi sebenarnya.
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Status change warning
                const statusSelect = document.getElementById('status');
                const originalStatus = statusSelect.value;

                statusSelect.addEventListener('change', function() {
                    if (this.value !== originalStatus) {
                        const confirmChange = confirm('Yakin ingin mengubah status assignment ini?');
                        if (!confirmChange) {
                            this.value = originalStatus;
                        }
                    }
                });

                // Auto-enable related fields based on status
                function toggleReviewFields() {
                    const status = statusSelect.value;
                    const kadivFields = document.querySelectorAll('#kadiv_rating, #kadiv_notes');
                    const directorFields = document.querySelectorAll('#director_rating, #director_notes');

                    // Enable/disable kadiv fields
                    kadivFields.forEach(field => {
                        if (['in_review_kadiv', 'in_review_director', 'rejected_kadiv', 'rejected_director',
                                'final'
                            ].includes(status)) {
                            field.disabled = false;
                            field.parentElement.style.opacity = '1';
                        } else {
                            field.disabled = true;
                            field.parentElement.style.opacity = '0.5';
                        }
                    });

                    // Enable/disable director fields
                    directorFields.forEach(field => {
                        if (['in_review_director', 'rejected_director', 'final'].includes(status)) {
                            field.disabled = false;
                            field.parentElement.style.opacity = '1';
                        } else {
                            field.disabled = true;
                            field.parentElement.style.opacity = '0.5';
                        }
                    });
                }

                statusSelect.addEventListener('change', toggleReviewFields);
                toggleReviewFields(); // Initial call

                // Form validation
                document.querySelector('form').addEventListener('submit', function(e) {
                    const status = statusSelect.value;

                    // Validate kadiv rating if status requires it
                    if (['in_review_director', 'final'].includes(status)) {
                        const kadivRating = document.getElementById('kadiv_rating').value;
                        if (!kadivRating) {
                            e.preventDefault();
                            alert('Rating Kepala Divisi harus diisi untuk status ini.');
                            return;
                        }
                    }

                    // Validate director rating if status requires it
                    if (status === 'final') {
                        const directorRating = document.getElementById('director_rating').value;
                        if (!directorRating) {
                            e.preventDefault();
                            alert('Rating Direktur harus diisi untuk status Final.');
                            return;
                        }
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .form-label {
                color: #5a5c69;
                font-size: 0.875rem;
            }

            .card-header h6 {
                font-size: 1rem;
            }

            .alert {
                font-size: 0.875rem;
            }

            @media (max-width: 991.98px) {
                .d-grid.gap-2 .btn {
                    margin-bottom: 0.5rem;
                }

                .card-body {
                    padding: 1rem;
                }
            }
        </style>
    @endpush
@endsection
