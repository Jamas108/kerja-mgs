@extends('layouts.director')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Setujui Pengajuan Promosi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Anda akan menyetujui pengajuan promosi untuk <strong>{{ $promotionRequest->employee->name }}</strong> dari divisi <strong>{{ $promotionRequest->employee->division->name }}</strong>.
                    </div>

                    <form action="{{ route('director.promotion_requests.approve', $promotionRequest) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Tambahkan catatan atau pesan untuk karyawan...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="signature_id" class="form-label">Pilih Tanda Tangan</label>
                            <div class="row">
                                @foreach($activeSignatures as $signature)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <img src="{{ $signature->image_url }}" alt="Tanda Tangan {{ $signature->title }}"
                                                    class="img-thumbnail mb-2" style="max-height: 100px;">
                                                <div class="form-check">
                                                    <input class="form-check-input signature-radio" type="radio"
                                                        name="signature_id" id="signature_{{ $signature->id }}"
                                                        value="{{ $signature->id }}"
                                                        data-promotion-id="{{ $promotionRequest->id }}"
                                                        {{ $signature->user_id == Auth::id() ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="signature_{{ $signature->id }}">
                                                        {{ $signature->title }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="mb-3">Preview Sertifikat:</h6>
                            <div id="certificate-preview" class="border p-3 bg-light" style="min-height: 200px;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-certificate fa-3x mb-2"></i>
                                    <p>Pilih tanda tangan untuk melihat preview sertifikat</p>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('director.promotion_requests.show', $promotionRequest) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Setujui Promosi & Generate Sertifikat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Load preview when a signature is selected
        $('.signature-radio').change(function() {
            const signatureId = $(this).val();
            const promotionId = $(this).data('promotion-id');

            if (signatureId) {
                $('#certificate-preview').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x my-3"></i><p>Memuat preview...</p></div>');

                $.ajax({
                    url: '{{ route('director.promotion_requests.preview-certificate') }}',
                    type: 'POST',
                    data: {
                        promotion_request_id: promotionId,
                        signature_id: signatureId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#certificate-preview').html(response);
                    },
                    error: function() {
                        $('#certificate-preview').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x my-3"></i><p>Gagal memuat preview sertifikat</p></div>');
                    }
                });
            }
        });

        // Trigger change on load if a signature is already selected
        $('.signature-radio:checked').trigger('change');
    });
</script>
@endpush
@endsection