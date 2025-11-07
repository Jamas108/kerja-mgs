<!-- resources/views/head_division/reviews/show.blade.php -->
@extends('layouts.head_division')

@section('title', 'Ulas Tugas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mb-3 mb-sm-0">Ulas Tugas: {{ $assignment->jobDesk->title }}</h1>
        <a href="{{ route('head_division.reviews.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Detail Tugas -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Tugas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold text-dark mb-2">Catatan Bukti</h5>
                        <p class="text-gray-800">{{ $assignment->evidence_note }}</p>
                    </div>
                    <div class="mb-3">
                        <h5 class="font-weight-bold text-dark mb-2">File Bukti</h5>
                        @if($assignment->evidence_file)
                            <div class="mb-3">
                                <a href="{{ asset('storage/' . $assignment->evidence_file) }}"
                                   class="btn btn-info btn-sm"
                                   target="_blank">
                                    <i class="fas fa-download"></i> Lihat/Unduh Bukti
                                </a>
                            </div>
                            @php
                                $fileExtension = pathinfo(storage_path('app/public/' . $assignment->evidence_file), PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
                            @endphp

                            @if($isImage)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $assignment->evidence_file) }}"
                                         class="img-fluid rounded border shadow-sm"
                                         alt="Gambar Bukti"
                                         style="max-height: 500px; object-fit: contain;">
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle"></i> Tidak ada file bukti yang diunggah.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Kirim Ulasan -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kirim Ulasan</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('head_division.reviews.submit', $assignment) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="rating" class="form-label font-weight-bold">Penilaian (1-4)</label>
                            <select class="form-control @error('rating') is-invalid @enderror"
                                    id="rating"
                                    name="rating"
                                    required>
                                <option value="">Pilih Penilaian</option>
                                <option value="1">1 - Buruk</option>
                                <option value="2">2 - Cukup</option>
                                <option value="3">3 - Baik</option>
                                <option value="4">4 - Sangat Baik</option>
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label font-weight-bold">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="4"
                                      placeholder="Tambahkan catatan ulasan Anda...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Keputusan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="radio"
                                       name="decision"
                                       id="approve"
                                       value="approve"
                                       checked>
                                <label class="form-check-label" for="approve">
                                    <i class="fas fa-check-circle text-success"></i> Setujui dan teruskan ke Direktur
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="decision"
                                       id="reject"
                                       value="reject">
                                <label class="form-check-label" for="reject">
                                    <i class="fas fa-times-circle text-danger"></i> Tolak dan kembalikan ke karyawan untuk revisi
                                </label>
                            </div>
                            @error('decision')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane"></i> Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Tambahan untuk Mobile -->
            <div class="card shadow mt-3 d-lg-none">
                <div class="card-body bg-light">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        <strong>Tips:</strong> Pastikan Anda telah memeriksa bukti dengan teliti sebelum memberikan penilaian.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .card-body h5 {
            font-size: 1rem;
        }

        .form-check-label {
            font-size: 0.875rem;
        }

        .btn-sm {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 575.98px) {
        .h3 {
            font-size: 1.25rem;
        }

        .card-header h6 {
            font-size: 0.9rem;
        }

        .btn-block {
            width: 100%;
        }
    }

    /* Improve form readability */
    .form-label {
        color: #5a5c69;
    }

    .form-check {
        padding: 0.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.15s ease-in-out;
    }

    .form-check:hover {
        background-color: #f8f9fc;
    }

    .form-check-input:checked ~ .form-check-label {
        font-weight: 600;
    }
</style>
@endpush
@endsection