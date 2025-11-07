@extends('layouts.director')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Kelola Tanda Tangan Saya</h5>
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                            data-bs-target="#addSignatureModal">
                            <i class="fas fa-plus"></i> Tambah Tanda Tangan
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($signatures->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Gambar</th>
                                            <th>Judul</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($signatures as $index => $signature)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <img src="{{ $signature->image_url }}" alt="Tanda Tangan"
                                                        style="height: 60px; max-width: 200px; background-color: #f8f9fa;">
                                                </td>
                                                <td>{{ $signature->title }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $signature->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $signature->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('director.signatures.edit', $signature) }}"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        {{-- <form
                                                            action="{{ route('director.signatures.toggle-active', $signature) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-info btn-sm">
                                                                <i
                                                                    class="fas {{ $signature->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                            </button>
                                                        </form> --}}
                                                        <form
                                                            action="{{ route('director.signatures.destroy', $signature) }}"
                                                            method="POST" class="d-inline signature-delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $signatures->links() }}
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-signature fa-3x text-muted mb-3"></i>
                                <h6>Belum ada tanda tangan yang ditambahkan.</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Signature Modal -->
    <div class="modal fade" id="addSignatureModal" tabindex="-1" aria-labelledby="addSignatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('director.signatures.store') }}" method="POST" id="signatureForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSignatureModalLabel">Tambah Tanda Tangan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul/Keterangan</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Tanda Tangan</label>
                            <div class="signature-pad-container border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Buat tanda tangan di area di bawah ini</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearButton">
                                        <i class="fas fa-eraser"></i> Hapus
                                    </button>
                                </div>
                                <div id="signature-pad-wrapper"
                                    style="border: 1px solid #ddd; background-color: white; width: 100%; height: 200px; position: relative;">
                                    <canvas id="signaturePad"
                                        style="position: absolute; left: 0; top: 0; width: 100%; height: 100%;"></canvas>
                                </div>
                                <input type="hidden" name="signature_data" id="signatureData">
                                @error('signature_data')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let signaturePad;
            const canvas = document.getElementById('signaturePad');
            const wrapper = document.getElementById('signature-pad-wrapper');
            const modal = document.getElementById('addSignatureModal');

            // Initialize signature pad when modal is shown
            modal.addEventListener('shown.bs.modal', function() {
                setTimeout(function() {
                    initializeSignaturePad();
                }, 100);
            });

            // Reset when modal is hidden
            modal.addEventListener('hidden.bs.modal', function() {
                if (signaturePad) {
                    signaturePad.clear();
                }
            });

            // Initialize the signature pad
            function initializeSignaturePad() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const width = wrapper.offsetWidth;
                const height = wrapper.offsetHeight;

                canvas.width = width * ratio;
                canvas.height = height * ratio;
                canvas.style.width = width + 'px';
                canvas.style.height = height + 'px';

                const context = canvas.getContext('2d');
                context.scale(ratio, ratio);

                if (signaturePad) {
                    signaturePad.off();
                }

                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 2.5,
                    velocityFilterWeight: 0.7
                });

                console.log("Canvas initialized - Width: " + width + ", Height: " + height);
            }

            // Clear button
            document.getElementById('clearButton').addEventListener('click', function(e) {
                e.preventDefault();
                if (signaturePad) {
                    signaturePad.clear();
                }
            });

            // Form submission
            document.getElementById('signatureForm').addEventListener('submit', function(e) {
                if (!signaturePad || signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Mohon buat tanda tangan terlebih dahulu!');
                    return false;
                }

                const signatureData = signaturePad.toDataURL('image/png');
                document.getElementById('signatureData').value = signatureData;
                console.log("Signature saved!");
            });

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.signature-delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('Anda yakin ingin menghapus tanda tangan ini?')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
