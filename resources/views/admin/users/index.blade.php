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

    {{-- @if(session('success'))
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
    @endif --}}

    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Pengguna</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Filter & Search untuk Mobile -->
            <div class="d-block d-lg-none mb-3">
                <!-- Search Box -->
                <div class="mb-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="mobileSearch" placeholder="Cari nama atau email...">
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row g-2">
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="filterRole">
                            <option value="">Semua Peran</option>
                            <option value="admin">Admin</option>
                            <option value="direktur">Direktur</option>
                            <option value="kepala divisi">Kepala Divisi</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="filterDivision">
                            <option value="">Semua Divisi</option>
                            @foreach($users->pluck('division')->unique()->filter() as $division)
                                <option value="{{ $division->name }}">{{ $division->name }}</option>
                            @endforeach
                            <option value="tidak ada">Tidak Ada Divisi</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Button -->
                <div class="mt-2">
                    <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters">
                        <i class="fas fa-redo"></i> Reset Filter
                    </button>
                </div>

                <!-- Result Counter -->
                <div class="mt-2">
                    <small class="text-muted">
                        Menampilkan <span id="resultCount">{{ $users->count() }}</span> dari {{ $users->count() }} pengguna
                    </small>
                </div>
            </div>

            <!-- Tampilan Mobile (Card) -->
            <div class="d-block d-lg-none" id="mobileUserList">
                @foreach($users as $user)
                <div class="card mb-3 border-start border-4 border-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'direktur' ? 'warning' : ($user->role->name === 'kepala divisi' ? 'info' : 'primary')) }} user-card"
                     data-name="{{ strtolower($user->name) }}"
                     data-email="{{ strtolower($user->email) }}"
                     data-role="{{ strtolower($user->role->name) }}"
                     data-division="{{ strtolower($user->division->name ?? 'tidak ada') }}">
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

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-4" style="display: none;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada pengguna yang sesuai dengan pencarian atau filter.</p>
                </div>
            </div>

            <!-- Filter untuk Desktop -->
            <div class="d-none d-lg-block mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="desktopFilterRole" class="form-label small mb-1">
                            <i class="fas fa-user-tag"></i> Filter Peran
                        </label>
                        <select class="form-select form-select-sm" id="desktopFilterRole">
                            <option value="">Semua Peran</option>
                            <option value="admin">Admin</option>
                            <option value="direktur">Direktur</option>
                            <option value="kepala divisi">Kepala Divisi</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="desktopFilterDivision" class="form-label small mb-1">
                            <i class="fas fa-building"></i> Filter Divisi
                        </label>
                        <select class="form-select form-select-sm" id="desktopFilterDivision">
                            <option value="">Semua Divisi</option>
                            @foreach($users->pluck('division')->unique()->filter() as $division)
                                <option value="{{ $division->name }}">{{ $division->name }}</option>
                            @endforeach
                            <option value="Tidak Ada">Tidak Ada Divisi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary btn-sm w-100" id="desktopResetFilters">
                            <i class="fas fa-redo"></i> Reset Filter
                        </button>
                    </div>
                </div>
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
        let dataTable;

        // Inisialisasi DataTable hanya untuk tampilan desktop
        if ($(window).width() >= 992) {
            dataTable = $('#dataTable').DataTable(
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

            // Custom filter function untuk DataTable
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const roleFilter = $('#desktopFilterRole').val().toLowerCase();
                    const divisionFilter = $('#desktopFilterDivision').val().toLowerCase();

                    const role = data[3].toLowerCase(); // Kolom Peran (index 3)
                    const division = data[4].toLowerCase(); // Kolom Divisi (index 4)

                    const matchRole = roleFilter === '' || role.includes(roleFilter);
                    const matchDivision = divisionFilter === '' || division.includes(divisionFilter);

                    return matchRole && matchDivision;
                }
            );

            // Event listener untuk filter desktop
            $('#desktopFilterRole, #desktopFilterDivision').on('change', function() {
                dataTable.draw();
            });

            // Reset filter desktop
            $('#desktopResetFilters').on('click', function() {
                $('#desktopFilterRole').val('');
                $('#desktopFilterDivision').val('');
                dataTable.draw();
            });
        }

        // Mobile Filter & Search Functionality
        function filterMobileUsers() {
            const searchTerm = $('#mobileSearch').val().toLowerCase();
            const roleFilter = $('#filterRole').val().toLowerCase();
            const divisionFilter = $('#filterDivision').val().toLowerCase();

            let visibleCount = 0;

            $('.user-card').each(function() {
                const card = $(this);
                const name = card.data('name');
                const email = card.data('email');
                const role = card.data('role');
                const division = card.data('division');

                // Check search term
                const matchSearch = searchTerm === '' ||
                                  name.includes(searchTerm) ||
                                  email.includes(searchTerm);

                // Check role filter
                const matchRole = roleFilter === '' || role === roleFilter;

                // Check division filter
                const matchDivision = divisionFilter === '' || division === divisionFilter;

                // Show/hide card based on all filters
                if (matchSearch && matchRole && matchDivision) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });

            // Update result count
            $('#resultCount').text(visibleCount);

            // Show/hide no results message
            if (visibleCount === 0) {
                $('#noResults').show();
            } else {
                $('#noResults').hide();
            }
        }

        // Event listeners for mobile filters
        $('#mobileSearch').on('keyup', filterMobileUsers);
        $('#filterRole').on('change', filterMobileUsers);
        $('#filterDivision').on('change', filterMobileUsers);

        // Reset filters mobile
        $('#resetFilters').on('click', function() {
            $('#mobileSearch').val('');
            $('#filterRole').val('');
            $('#filterDivision').val('');
            filterMobileUsers();
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush