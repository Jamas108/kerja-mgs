@extends('layouts.head_division')

@section('title', 'Bandingkan Kinerja Karyawan')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .btn-sm-mobile {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
        }
    }

    @media (max-width: 575.98px) {
        .page-header h2 {
            font-size: 1.25rem;
        }

        .d-grid .btn {
            font-size: 0.875rem;
        }
    }

    /* Chart responsive */
    .chart-container {
        position: relative;
        height: 300px;
    }

    @media (max-width: 767.98px) {
        .chart-container {
            height: 250px;
        }
    }

    .chart-container-horizontal {
        position: relative;
        height: 200px;
    }

    @media (max-width: 767.98px) {
        .chart-container-horizontal {
            height: 250px;
        }
    }

    /* Select2 Responsive */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    @media (max-width: 768px) {
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Table mobile card view */
    .compare-card-mobile {
        border-left: 4px solid #4e73df;
    }

    @media (max-width: 767.98px) {
        .form-text {
            font-size: 0.75rem;
        }
    }

    /* Form styling */
    .form-label {
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #5e72e4;
        box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
    }

    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 mb-2 text-gray-800 fw-bold">{{ __('Bandingkan Kinerja Karyawan') }}</h2>
                <p class="text-secondary mb-0">Analisis dan bandingkan kinerja antar karyawan dalam divisi Anda</p>
            </div>
            <div>
                <a href="{{ route('head_division.performances.index') }}" class="btn btn-secondary btn-sm-mobile">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Pilih Karyawan -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-users me-2 text-primary"></i>
                Pilih Karyawan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('head_division.performances.compare.post') }}" method="post" id="compareForm">
                @csrf
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="mb-3">
                            <label for="employee_ids" class="form-label required">Pilih Karyawan untuk Dibandingkan</label>
                            <select class="form-select @error('employee_ids') is-invalid @enderror"
                                    name="employee_ids[]"
                                    id="employee_ids"
                                    multiple
                                    required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ in_array($employee->id, old('employee_ids', isset($selectedEmployees) ? collect($selectedEmployees)->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_ids')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Pilih 2-5 karyawan untuk perbandingan kinerja
                            </small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-line me-2"></i>Bandingkan Kinerja
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($selectedEmployees))
        <!-- Chart Perbandingan Nilai -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-header-title mb-0">
                            <i class="fas fa-trophy me-2 text-info"></i>
                            Perbandingan Nilai Kinerja Karyawan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-horizontal">
                            <canvas id="compareOverallChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Perkembangan Bulanan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-header-title mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Perkembangan Kinerja Bulanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="compareMonthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Kinerja -->
        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="card-header-title mb-0">
                        <i class="fas fa-tasks me-2 text-primary"></i>
                        Detail Kinerja Karyawan
                    </h5>
                    <span class="badge bg-primary-light text-primary py-2 px-3">
                        <i class="fas fa-users me-1"></i>
                        {{ count($selectedEmployees) }} Karyawan
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Desktop View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover" id="compare-employees-table">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Nilai Rata-Rata</th>
                                <th>Kategori</th>
                                <th>Total Tugas Selesai</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedEmployees as $employee)
                                @php
                                    $performanceInfo = $performanceData[$employee->id] ?? null;
                                    $overallPerformance = $performanceInfo['overall'] ?? null;

                                    $badgeClass = 'bg-secondary';
                                    $category = 'Belum Ada Penilaian';

                                    if ($overallPerformance !== null) {
                                        if ($overallPerformance >= 3.7) {
                                            $badgeClass = 'bg-success';
                                            $category = 'Sangat Baik';
                                        } elseif ($overallPerformance >= 3) {
                                            $badgeClass = 'bg-info';
                                            $category = 'Baik';
                                        } elseif ($overallPerformance >= 2.5) {
                                            $badgeClass = 'bg-primary';
                                            $category = 'Cukup';
                                        } elseif ($overallPerformance >= 2) {
                                            $badgeClass = 'bg-warning';
                                            $category = 'Kurang';
                                        } else {
                                            $badgeClass = 'bg-danger';
                                            $category = 'Sangat Kurang';
                                        }
                                    }

                                    $totalTasks = isset($performanceInfo['monthly'])
                                        ? collect($performanceInfo['monthly'])->sum('total_tasks')
                                        : 0;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $employee->name }}</td>
                                    <td>
                                        @if ($overallPerformance !== null)
                                            <span class="badge {{ $badgeClass }}">
                                                {{ number_format($overallPerformance, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">{{ $category }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-clipboard-check me-1"></i>
                                            {{ $totalTasks }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('head_division.performances.show', $employee->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile/Tablet View -->
                <div class="d-lg-none">
                    @foreach($selectedEmployees as $employee)
                        @php
                            $performanceInfo = $performanceData[$employee->id] ?? null;
                            $overallPerformance = $performanceInfo['overall'] ?? null;

                            $badgeClass = 'bg-secondary';
                            $category = 'Belum Ada Penilaian';

                            if ($overallPerformance !== null) {
                                if ($overallPerformance >= 3.7) {
                                    $badgeClass = 'bg-success';
                                    $category = 'Sangat Baik';
                                } elseif ($overallPerformance >= 3) {
                                    $badgeClass = 'bg-info';
                                    $category = 'Baik';
                                } elseif ($overallPerformance >= 2.5) {
                                    $badgeClass = 'bg-primary';
                                    $category = 'Cukup';
                                } elseif ($overallPerformance >= 2) {
                                    $badgeClass = 'bg-warning';
                                    $category = 'Kurang';
                                } else {
                                    $badgeClass = 'bg-danger';
                                    $category = 'Sangat Kurang';
                                }
                            }

                            $totalTasks = isset($performanceInfo['monthly'])
                                ? collect($performanceInfo['monthly'])->sum('total_tasks')
                                : 0;
                        @endphp
                        <div class="card mb-3 compare-card-mobile shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">{{ $employee->name }}</h6>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">Nilai Rata-Rata</small>
                                        @if ($overallPerformance !== null)
                                            <span class="badge {{ $badgeClass }}">
                                                {{ number_format($overallPerformance, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">Total Tugas</small>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-clipboard-check me-1"></i>
                                            {{ $totalTasks }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Kategori</small>
                                    <span class="badge {{ $badgeClass }} w-100 py-2">{{ $category }}</span>
                                </div>

                                <a href="{{ route('head_division.performances.show', $employee->id) }}"
                                    class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<!-- jQuery (required for DataTables & Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#employee_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih karyawan...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Tidak ada karyawan yang ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });

        // Form validation
        const form = document.getElementById('compareForm');
        form.addEventListener('submit', function(event) {
            const selectedEmployees = $('#employee_ids').val();

            if (!selectedEmployees || selectedEmployees.length === 0) {
                event.preventDefault();
                event.stopPropagation();

                const employeeSelect = document.getElementById('employee_ids');
                employeeSelect.classList.add('is-invalid');

                let errorDiv = employeeSelect.parentElement.querySelector('.invalid-feedback');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    employeeSelect.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Silakan pilih minimal 2 karyawan';
                errorDiv.style.display = 'block';

                return false;
            }

            if (selectedEmployees.length < 2 || selectedEmployees.length > 5) {
                event.preventDefault();
                event.stopPropagation();

                const employeeSelect = document.getElementById('employee_ids');
                employeeSelect.classList.add('is-invalid');

                let errorDiv = employeeSelect.parentElement.querySelector('.invalid-feedback');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    employeeSelect.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Silakan pilih 2-5 karyawan untuk dibandingkan';
                errorDiv.style.display = 'block';

                return false;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
        });

        // Remove invalid state when user selects employees
        $('#employee_ids').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.invalid-feedback').hide();
            }
        });

        @if(!empty($selectedEmployees))
        // Initialize DataTable only on desktop
        if ($(window).width() >= 992) {
            $('#compare-employees-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
                "initComplete": function() {
                    $('.dataTables_filter input').attr('placeholder', 'Cari karyawan...');
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_filter label').addClass('mb-0');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });
        }
        @endif
    });
</script>

@if(!empty($selectedEmployees))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isMobile = window.innerWidth < 768;

        // Data untuk chart
        const performanceData = @json($performanceData);
        const employees = @json($selectedEmployees);

        // Chart untuk perbandingan nilai keseluruhan
        const ctxOverall = document.getElementById('compareOverallChart').getContext('2d');

        const overallLabels = employees.map(employee => employee.name);
        const overallData = employees.map(employee => {
            const data = performanceData[employee.id];
            return data && data.overall ? data.overall : 0;
        });

        const overallColors = employees.map(employee => {
            const data = performanceData[employee.id];
            const score = data && data.overall ? data.overall : 0;

            if (score >= 3.7) return 'rgba(40, 167, 69, 0.8)';
            if (score >= 3) return 'rgba(23, 162, 184, 0.8)';
            if (score >= 2.5) return 'rgba(0, 123, 255, 0.8)';
            if (score >= 2) return 'rgba(255, 193, 7, 0.8)';
            return 'rgba(220, 53, 69, 0.8)';
        });

        new Chart(ctxOverall, {
            type: 'bar',
            data: {
                labels: overallLabels,
                datasets: [{
                    label: 'Nilai Kinerja Keseluruhan',
                    data: overallData,
                    backgroundColor: overallColors,
                    borderColor: overallColors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 4,
                        title: {
                            display: !isMobile,
                            text: 'Nilai Kinerja'
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Chart untuk perkembangan bulanan
        const ctxMonthly = document.getElementById('compareMonthlyChart').getContext('2d');

        let allPeriods = [];
        employees.forEach(employee => {
            const data = performanceData[employee.id];
            if (data && data.monthly) {
                data.monthly.forEach(item => {
                    if (!allPeriods.includes(item.period)) {
                        allPeriods.push(item.period);
                    }
                });
            }
        });

        allPeriods.sort((a, b) => {
            const [aMonth, aYear] = a.split(' ');
            const [bMonth, bYear] = b.split(' ');
            const months = ["January", "February", "March", "April", "May", "June",
                           "July", "August", "September", "October", "November", "December"];

            if (aYear !== bYear) {
                return parseInt(aYear) - parseInt(bYear);
            }
            return months.indexOf(aMonth) - months.indexOf(bMonth);
        });

        const monthlyDatasets = [];
        const colors = [
            'rgb(75, 192, 192)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)'
        ];

        employees.forEach((employee, index) => {
            const data = performanceData[employee.id];
            let monthlyData = [];

            if (data && data.monthly) {
                monthlyData = allPeriods.map(period => {
                    const found = data.monthly.find(item => item.period === period);
                    return found ? found.average_score : null;
                });
            }

            monthlyDatasets.push({
                label: employee.name,
                data: monthlyData,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
                tension: 0.3,
                fill: false,
                borderWidth: 3
            });
        });

        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: allPeriods,
                datasets: monthlyDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 1,
                        max: 4,
                        title: {
                            display: !isMobile,
                            text: 'Nilai Kinerja'
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'top',
                        labels: {
                            font: {
                                size: isMobile ? 10 : 12
                            },
                            padding: isMobile ? 10 : 15
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
@endsection