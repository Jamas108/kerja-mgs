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
@endsection