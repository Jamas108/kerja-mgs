@extends('layouts.admin')

@section('title', 'Kelola Divisi')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-3 mb-md-4">
        <h1 class="h3 mb-2 mb-sm-0 text-gray-800">Kelola Divisi</h1>
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary btn-sm btn-md-md">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Divisi Baru</span><span class="d-inline d-sm-none">Tambah</span>
        </a>
    </div>

    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Divisi</h6>
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
                        <input type="text" class="form-control" id="mobileSearch" placeholder="Cari nama divisi atau kepala divisi...">
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row g-2">
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="filterHead">
                            <option value="">Semua Status Kepala</option>
                            <option value="ada">Ada Kepala Divisi</option>
                            <option value="kosong">Belum Ada Kepala</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="filterEmployees">
                            <option value="">Semua Karyawan</option>
                            <option value="0">0 Karyawan</option>
                            <option value="1-5">1-5 Karyawan</option>
                            <option value="6-10">6-10 Karyawan</option>
                            <option value="11+">11+ Karyawan</option>
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
                        Menampilkan <span id="resultCount">{{ $divisions->count() }}</span> dari {{ $divisions->count() }} divisi
                    </small>
                </div>
            </div>

            <!-- Tampilan Mobile (Card) -->
            <div class="d-block d-lg-none" id="mobileDivisionList">
                @foreach($divisions as $division)
                @php
                    $divisionHead = $division->users()->where('role_id', 4)->first();
                    $headStatus = $divisionHead ? 'ada' : 'kosong';
                    $employeeCount = $division->users_count;
                    $employeeRange = $employeeCount == 0 ? '0' :
                                    ($employeeCount <= 5 ? '1-5' :
                                    ($employeeCount <= 10 ? '6-10' : '11+'));
                @endphp
                <div class="card mb-3 border-start border-4 border-primary division-card"
                     data-name="{{ strtolower($division->name) }}"
                     data-head="{{ $headStatus }}"
                     data-head-name="{{ $divisionHead ? strtolower($divisionHead->name) : '' }}"
                     data-employees="{{ $employeeRange }}"
                     data-employee-count="{{ $employeeCount }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold">{{ $division->name }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i>
                                    {{ $divisionHead ? $divisionHead->name : 'Belum Ditugaskan' }}
                                </small>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4">
                                <small class="text-muted d-block">Karyawan</small>
                                <span class="font-weight-bold">{{ $division->users_count }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Tugas</small>
                                <span class="font-weight-bold">{{ $division->jobDesks()->count() }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Dibuat</small>
                                <span class="font-weight-bold">{{ $division->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        @php
                            $totalAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                $query->where('division_id', $division->id);
                            })->count();

                            $completedAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                $query->where('division_id', $division->id);
                            })->where('status', 'final')->count();

                            $completionPercentage = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;
                        @endphp

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Progress Penyelesaian</small>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%"
                                    aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($completionPercentage, 0) }}%
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Lihat</span>
                            </a>
                            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm flex-fill flex-sm-grow-0">
                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
                            </a>
                            <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="d-inline flex-fill flex-sm-grow-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
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
                    <p class="text-muted">Tidak ada divisi yang sesuai dengan pencarian atau filter.</p>
                </div>
            </div>

            <!-- Filter untuk Desktop -->
            <div class="d-none d-lg-block mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="desktopFilterHead" class="form-label small mb-1">
                            <i class="fas fa-user-tie"></i> Filter Status Kepala Divisi
                        </label>
                        <select class="form-select form-select-sm" id="desktopFilterHead">
                            <option value="">Semua Status</option>
                            <option value="Ada">Ada Kepala Divisi</option>
                            <option value="Belum">Belum Ada Kepala</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="desktopFilterEmployees" class="form-label small mb-1">
                            <i class="fas fa-users"></i> Filter Jumlah Karyawan
                        </label>
                        <select class="form-select form-select-sm" id="desktopFilterEmployees">
                            <option value="">Semua Jumlah</option>
                            <option value="0">0 Karyawan</option>
                            <option value="1-5">1-5 Karyawan</option>
                            <option value="6-10">6-10 Karyawan</option>
                            <option value="11+">11+ Karyawan</option>
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
                            <th style="width: 10%;">Karyawan</th>
                            <th style="width: 20%;">Kepala Divisi</th>
                            <th style="width: 10%;">Total Tugas</th>
                            <th style="width: 15%;">Dibuat Pada</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $division)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $division->name }}</td>
                            <td class="text-center">{{ $division->users_count }}</td>
                            <td>
                                @php
                                    $divisionHead = $division->users()->where('role_id', 4)->first();
                                @endphp
                                {{ $divisionHead ? $divisionHead->name : 'Belum Ditugaskan' }}
                            </td>
                            <td class="text-center">{{ $division->jobDesks()->count() }}</td>
                            <td>{{ $division->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
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

            @if($divisions->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada divisi yang terdaftar.</p>
                <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Divisi Pertama
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Overview Card -->
    <div class="card shadow mb-3 mb-md-4">
        <div class="card-header py-2 py-md-3">
            <h6 class="m-0 font-weight-bold text-primary">Ringkasan Divisi</h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row">
                @foreach($divisions as $division)
                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="m-0 font-weight-bold text-truncate">{{ $division->name }}</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-center flex-fill">
                                    <h5 class="mb-1 small">Karyawan</h5>
                                    <p class="mb-0 h5 text-primary">{{ $division->users_count }}</p>
                                </div>
                                <div class="text-center flex-fill border-start border-end">
                                    <h5 class="mb-1 small">Tugas</h5>
                                    <p class="mb-0 h5 text-primary">{{ $division->jobDesks()->count() }}</p>
                                </div>
                                <div class="text-center flex-fill">
                                    <h5 class="mb-1 small">Kepala</h5>
                                    @php
                                        $divisionHead = $division->users()->where('role_id', 4)->first();
                                    @endphp
                                    <p class="mb-0 h5 text-primary">{{ $divisionHead ? 'Ada' : 'Belum' }}</p>
                                </div>
                            </div>

                            <small class="text-muted d-block mb-1">Progress Penyelesaian</small>
                            <div class="progress" style="height: 24px;">
                                @php
                                    $totalAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                        $query->where('division_id', $division->id);
                                    })->count();

                                    $completedAssignments = \App\Models\EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
                                        $query->where('division_id', $division->id);
                                    })->where('status', 'final')->count();

                                    $completionPercentage = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%"
                                    aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($completionPercentage, 0) }}%
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-2">
                            <div class="btn-group d-flex" role="group">
                                <a href="{{ route('admin.divisions.show', $division) }}" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-eye"></i> <span class="d-none d-md-inline">Lihat</span>
                                </a>
                                <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-info btn-sm flex-fill">
                                    <i class="fas fa-edit"></i> <span class="d-none d-md-inline">Edit</span>
                                </a>
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
                    const headFilter = $('#desktopFilterHead').val();
                    const employeesFilter = $('#desktopFilterEmployees').val();

                    const headText = data[3]; // Kolom Kepala Divisi (index 3)
                    const employeeCount = parseInt(data[2]); // Kolom Karyawan (index 2)

                    // Filter Kepala Divisi
                    let matchHead = true;
                    if (headFilter === 'Ada') {
                        matchHead = headText !== 'Belum Ditugaskan';
                    } else if (headFilter === 'Belum') {
                        matchHead = headText === 'Belum Ditugaskan';
                    }

                    // Filter Jumlah Karyawan
                    let matchEmployees = true;
                    if (employeesFilter === '0') {
                        matchEmployees = employeeCount === 0;
                    } else if (employeesFilter === '1-5') {
                        matchEmployees = employeeCount >= 1 && employeeCount <= 5;
                    } else if (employeesFilter === '6-10') {
                        matchEmployees = employeeCount >= 6 && employeeCount <= 10;
                    } else if (employeesFilter === '11+') {
                        matchEmployees = employeeCount >= 11;
                    }

                    return matchHead && matchEmployees;
                }
            );

            // Event listener untuk filter desktop
            $('#desktopFilterHead, #desktopFilterEmployees').on('change', function() {
                dataTable.draw();
            });

            // Reset filter desktop
            $('#desktopResetFilters').on('click', function() {
                $('#desktopFilterHead').val('');
                $('#desktopFilterEmployees').val('');
                dataTable.draw();
            });
        }

        // Mobile Filter & Search Functionality
        function filterMobileDivisions() {
            const searchTerm = $('#mobileSearch').val().toLowerCase();
            const headFilter = $('#filterHead').val();
            const employeesFilter = $('#filterEmployees').val();

            let visibleCount = 0;

            $('.division-card').each(function() {
                const card = $(this);
                const name = card.data('name');
                const headName = card.data('head-name');
                const headStatus = card.data('head');
                const employeeRange = card.data('employees');
                const employeeCount = parseInt(card.data('employee-count'));

                // Check search term
                const matchSearch = searchTerm === '' ||
                                  name.includes(searchTerm) ||
                                  headName.includes(searchTerm);

                // Check head filter
                let matchHead = true;
                if (headFilter === 'ada') {
                    matchHead = headStatus === 'ada';
                } else if (headFilter === 'kosong') {
                    matchHead = headStatus === 'kosong';
                }

                // Check employees filter
                let matchEmployees = true;
                if (employeesFilter !== '') {
                    matchEmployees = employeeRange === employeesFilter;
                }

                // Show/hide card based on all filters
                if (matchSearch && matchHead && matchEmployees) {
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
        $('#mobileSearch').on('keyup', filterMobileDivisions);
        $('#filterHead').on('change', filterMobileDivisions);
        $('#filterEmployees').on('change', filterMobileDivisions);

        // Reset filters mobile
        $('#resetFilters').on('click', function() {
            $('#mobileSearch').val('');
            $('#filterHead').val('');
            $('#filterEmployees').val('');
            filterMobileDivisions();
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush