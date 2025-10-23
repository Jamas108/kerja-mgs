@extends('layouts.director')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Tanda Tangan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('director.signatures.update', $signature) }}" method="POST" id="editSignatureForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Pemilik (Direktur)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">-- Pilih Direktur --</option>
                                @foreach($directors as $director)
                                    <option value="{{ $director->id }}" {{ $signature->user_id == $director->id ? 'selected' : '' }}>
                                        {{ $director->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul/Keterangan</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $signature->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanda Tangan Saat Ini</label>
                            <div class="mb-2">
                                <img src="{{ $signature->image_url }}" alt="Tanda Tangan" class="img-thumbnail" style="height: 100px; max-width: 300px; background-color: #f8f9fa;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Buat Tanda Tangan Baru (Opsional)</label>
                            <div class="signature-pad-container border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Buat tanda tangan di area di bawah ini</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearButton">
                                        <i class="fas fa-eraser"></i> Hapus
                                    </button>
                                </div>
                                <canvas id="signaturePad" class="signature-pad border rounded w-100" height="200"></canvas>
                                <input type="hidden" name="signature_data" id="signatureData">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah tanda tangan.</small>
                                @error('signature_data')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ $signature->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('director.signatures.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Signature pad initialization
        const canvas = document.getElementById('signaturePad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Clear button
        document.getElementById('clearButton').addEventListener('click', function() {
            signaturePad.clear();
        });

        // Adjust canvas size based on container
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Clear the canvas after resize
        }

        // Resize canvas initially and on window resize
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Form submission - save signature data
        document.getElementById('editSignatureForm').addEventListener('submit', function(e) {
            // Only set signature data if the pad has been used
            if (!signaturePad.isEmpty()) {
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signatureData').value = signatureData;
            }
            return true;
        });
    });
</script>
@endpush