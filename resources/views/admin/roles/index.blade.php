@extends('layouts.admin')

@section('title', 'Kelola Peran ')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Kelola Peran</h1>
        {{-- <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm btn-md-md">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New Role</span><span class="d-inline d-sm-none">Add</span>
        </a> --}}
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Peran</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Tampilan Mobile (Card) -->
            <div class="d-block d-lg-none">
                @foreach($roles as $role)
                <div class="card mb-3 border-start border-4 border-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }} mb-2">
                                    {{ ucfirst($role->name) }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block">Total Pengguna</small>
                            <h5 class="mb-0 font-weight-bold">{{ $role->users()->count() }}</h5>
                        </div>

                        {{-- <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-info btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
                            </a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline flex-fill flex-sm-grow-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this role?')">
                                    <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tampilan Desktop (Table) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 10%;">No</th>
                            <th style="width: 50%;">Nama</th>
                            <th class="text-center" style="width: 40%;">Total Pengguna</th>
                            {{-- <th style="width: 20%;">Created At</th> --}}
                            {{-- <th style="width: 15%;">Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            </td>
                            <td class="text-center font-weight-bold">{{ $role->users()->count() }}</td>
                            {{-- <td>{{ $role->created_at->format('d M Y') }}</td> --}}
                            {{-- <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this role?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($roles->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                <p class="text-muted">No roles registered yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Roles Statistics -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Statistik Peran</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row">
                @foreach($roles as $role)
                <div class="col-6 col-md-4 col-lg-3 mb-3 mb-md-4">
                    <div class="card h-100 border-0 shadow-sm border-start border-4 border-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }} mb-2">
                                    {{ ucfirst($role->name) }}
                                </span>
                                <h3 class="mb-0 font-weight-bold text-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'direktur' ? 'warning' : ($role->name === 'kepala divisi' ? 'info' : 'primary')) }}">
                                    {{ $role->users()->count() }}
                                </h3>
                                <small class="text-muted">Total Pengguna</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
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
        );
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush