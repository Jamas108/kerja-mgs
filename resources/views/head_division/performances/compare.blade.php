@extends('layouts.head_division')

@section('title', 'Bandingkan Kinerja Karyawan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Bandingkan Kinerja Karyawan') }}</h2>
        <p class="text-secondary mb-0">Analisis dan bandingkan kinerja antar karyawan dalam divisi Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('head_division.performances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-header-title">
            <i class="fas fa-users me-2 text-primary"></i>
            Pilih Karyawan
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('head_division.performances.compare.post') }}" method="post">
            @csrf
            <div class="row mb-4">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group">
                        <label for="employee_ids" class="form-label">Pilih Karyawan untuk Dibandingkan:</label>
                        <select class="form-select" name="employee_ids[]" id="employee_ids" multiple required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employee_ids', isset($selectedEmployees) ? collect($selectedEmployees)->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">
                            Pilih 2-5 karyawan untuk perbandingan kinerja
                        </div>
                    </div>
                    <div class="d-grid mt-3">
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
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">
                        <i class="fas fa-trophy me-2 text-info"></i>
                        Perbandingan Nilai Kinerja Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="compareOverallChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Perkembangan Kinerja Bulanan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="compareMonthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">
                <i class="fas fa-tasks me-2 text-primary"></i>
                Detail Kinerja Karyawan
            </h5>
            <div class="card-header-actions">
                <span class="badge bg-primary-light text-primary py-2 px-3">
                    <i class="fas fa-users me-1"></i>
                    {{ count($selectedEmployees) }} Karyawan
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
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
        </div>
    </div>
@endif

@push('scripts')
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

@if(!empty($selectedEmployees))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#compare-employees-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            "search": {
                "smart": true,
                "caseInsensitive": true
            },
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
            "initComplete": function() {
                // Customize search input
                $('.dataTables_filter input').attr('placeholder', 'Cari karyawan...');
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_filter label').addClass('mb-0');

                // Customize length menu
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });

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
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 4,
                        title: {
                            display: true,
                            text: 'Nilai Kinerja'
                        }
                    }
                }
            }
        });

        // Chart untuk perkembangan bulanan
        const ctxMonthly = document.getElementById('compareMonthlyChart').getContext('2d');

        // Kumpulkan semua periode unik dari semua karyawan
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

        // Urutkan periode secara kronologis
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

        // Buat dataset untuk setiap karyawan
        const monthlyDatasets = [];

        // Warna untuk grafik
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
                // Isi data untuk setiap periode
                monthlyData = allPeriods.map(period => {
                    const found = data.monthly.find(item => item.period === period);
                    return found ? found.average_score : null;
                });
            }

            // Dataset untuk nilai kinerja
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
                            display: true,
                            text: 'Nilai Kinerja'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
@else
<script>
    $(document).ready(function() {
        // Initialize Bootstrap Select2
        if ($.fn.select2) {
            $('#employee_ids').select2({
                placeholder: 'Pilih karyawan...',
                allowClear: true
            });
        }
    });
</script>
@endif
@endpush
@endsection