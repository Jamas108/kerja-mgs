@extends('layouts.head_division')

@section('title', 'Pengajuan Promosi Karyawan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Pengajuan Promosi Karyawan</h2>
        <p class="text-secondary mb-0">Ajukan promosi untuk karyawan berprestasi</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('head_division.performances.show', $employee->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Profil Karyawan
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="https://ui-avatars.com/api/?name={{ $employee->name }}&background=random&size=100" class="rounded-circle mb-3" alt="{{ $employee->name }}">
                <h4 class="fw-bold mb-1">{{ $employee->name }}</h4>
                <p class="text-muted mb-3">{{ $employee->email }}</p>

                @php
                    // Default variables
                    $performanceScore = $performanceScore ?? null;
                    $performanceCategory = $performanceCategory ?? null;
                @endphp

                @if ($performanceScore)
                    <div class="mt-4">
                        <h2 class="display-4 mb-0">{{ $performanceScore }}</h2>
                        <p class="lead">dari 4.00</p>

                        @php
                            $badgeClass = 'bg-danger';

                            if ($performanceScore >= 3.7) {
                                $badgeClass = 'bg-success';
                            } elseif ($performanceScore >= 3) {
                                $badgeClass = 'bg-info';
                            } elseif ($performanceScore >= 2.5) {
                                $badgeClass = 'bg-primary';
                            } elseif ($performanceScore >= 2) {
                                $badgeClass = 'bg-warning';
                            }
                        @endphp

                        <h4><span class="badge {{ $badgeClass }}">{{ $performanceCategory }}</span></h4>
                    </div>
                @else
                    <div class="mt-4">
                        <h4><span class="badge bg-secondary">Belum Ada Penilaian</span></h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-header-title">
                    <i class="fas fa-award me-2 text-warning"></i>
                    Formulir Pengajuan Promosi
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-1">Catatan:</p>
                        <p class="mb-0">Pengajuan promosi hanya dapat dilakukan untuk karyawan dengan kinerja "Baik" atau "Sangat Baik". Pengajuan akan diteruskan ke Direktur untuk persetujuan.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('head_division.performances.store_promotion', $employee->id) }}" enctype="multipart/form-data" class="mt-4">
                    @csrf

                    <div class="mb-4">
                        <label for="period" class="form-label fw-bold">Periode Penilaian</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="period_start" class="form-label text-muted small">Dari Bulan</label>
                                <input type="month" id="period_start" name="period_start" class="form-control @error('period_start') is-invalid @enderror" value="{{ old('period_start') }}" required>
                                @error('period_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="period_end" class="form-label text-muted small">Sampai Bulan</label>
                                <input type="month" id="period_end" name="period_end" class="form-control @error('period_end') is-invalid @enderror" value="{{ old('period_end') }}" required>
                                @error('period_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            Pilih periode penilaian kinerja untuk promosi ini (contoh: Januari 2024 - Agustus 2024)
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reason" class="form-label fw-bold">Alasan Pengajuan Promosi</label>
                        <textarea id="reason" name="reason" rows="6" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb text-warning me-1"></i>
                            Jelaskan secara detail mengapa karyawan ini layak mendapatkan promosi. Sertakan prestasi, peningkatan kinerja, dan kontribusi penting yang telah diberikan.
                        </div>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="supporting_document" class="form-label fw-bold">Dokumen Pendukung (opsional)</label>
                        <input type="file" id="supporting_document" name="supporting_document" class="form-control @error('supporting_document') is-invalid @enderror">
                        <div class="form-text">
                            <i class="fas fa-file-pdf text-danger me-1"></i>
                            Unggah dokumen pendukung jika ada (format PDF, DOC, DOCX, maks. 2MB)
                        </div>
                        @error('supporting_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan Promosi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection