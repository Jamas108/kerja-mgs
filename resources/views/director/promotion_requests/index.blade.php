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

            <!-- Pending Requests -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Pengajuan Promosi Menunggu Persetujuan</h5>
                </div>
                <div class="card-body">
                    @if($pendingRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Divisi</th>
                                        <th>Periode</th>
                                        <th>Diajukan Oleh</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                    <tr>
                                        <td>{{ $request->employee->name }}</td>
                                        <td>{{ $request->employee->division->name }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $request->period ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $request->requester->name }}</td>
                                        <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('director.promotion_requests.show', $request) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6>Tidak ada pengajuan promosi yang menunggu persetujuan.</h6>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recently Processed Requests -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Pengajuan Promosi yang Telah Diproses</h5>
                </div>
                <div class="card-body">
                    @if($processedRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Divisi</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Tanggal Diproses</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($processedRequests as $request)
                                    <tr>
                                        <td>{{ $request->employee->name }}</td>
                                        <td>{{ $request->employee->division->name }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $request->period ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{!! $request->status_badge !!}</td>
                                        <td>{{ $request->reviewed_at ? $request->reviewed_at->format('d M Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('director.promotion_requests.show', $request) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">Belum ada pengajuan promosi yang telah diproses.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection