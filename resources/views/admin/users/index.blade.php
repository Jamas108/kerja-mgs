<!-- resources/views/admin/users/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Kelola Pengguna</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm btn-md-md">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambahkan Pengguna</span><span class="d-inline d-sm-none">Tambah</span>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Pengguna</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Tampilan Mobile (Card) -->
            <div class="d-block d-lg-none">
                @foreach($users as $user)
                <div class="card mb-3 border-start border-4 border-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold">{{ $user->name }}</h6>
                                <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($user->role->name) }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i> {{ $user->email }}</small>
                            <small class="text-muted d-block"><i class="fas fa-building me-1"></i> {{ $user->division->name ?? 'Tidak Ada' }}</small>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Lihat</span>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline flex-fill flex-sm-grow-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                    <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tampilan Desktop (Table) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Nama</th>
                            <th style="width: 25%;">Email</th>
                            <th style="width: 15%;">Peran</th>
                            <th style="width: 15%;">Divisi</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($user->role->name) }}
                                </span>
                            </td>
                            <td>{{ $user->division->name ?? 'Tidak Ada' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
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

            @if($users->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada pengguna yang terdaftar.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pengguna Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable hanya untuk tampilan desktop
        if ($(window).width() >= 992) {
            $('#dataTable').DataTable(
            //     {
            //     "language": {
            //         "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            //     },
            //     "pageLength": 10,
            //     "ordering": true,
            //     "searching": true,
            //     "info": true,
            //     "responsive": true
            // }
        );
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush