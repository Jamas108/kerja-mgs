@extends('layouts.head_division')

@section('title', 'Laporan Kinerja Divisi')

@push('styles')
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
    }

    @media (max-width: 575.98px) {
        .display-5 {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 1.5rem;
        }

        h3 {
            font-size: 1.25rem;
        }

        h5 {
            font-size: 0.95rem;
        }

        h6 {
            font-size: 0.85rem;
        }

        small {
            font-size: 0.75rem;
        }
    }

    /* Card spacing for mobile */
    @media (max-width: 991.98px) {
        .col-md-4, .col-md-8 {
            margin-bottom: 1rem;
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

    /* Stat cards mobile */
    @media (max-width: 767.98px) {
        .col-6 .card-body {
            padding: 0.75rem;
        }
    }

    /* Print styles */
    @media print {
        .btn,
        .page-header .d-flex > div:last-child {
            display: none !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
        }

        body {
            padding: 20px !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }

        .chart-container {
            height: 300px !important;
        }

        h1:first-of-type {
            margin-top: 0 !important;
        }

        .row {
            page-break-inside: avoid;
        }

        /* Force desktop layout for print */
        .d-lg-none {
            display: none !important;
        }

        .d-none.d-lg-flex {
            display: flex !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="flex-grow-1">
                <h2 class="h3 mb-2 text-gray-800 fw-bold">Laporan Kinerja Divisi</h2>
                <p class="text-secondary mb-0">Analisis komprehensif kinerja seluruh karyawan dalam divisi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('head_division.performances.index') }}" class="btn btn-secondary btn-sm-mobile">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <button class="btn btn-primary btn-sm-mobile" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <!-- Total Karyawan -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-header-title mb-0">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Karyawan Divisi
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="display-5 mb-2">{{ $totalEmployees }}</h2>
                            <p class="text-muted mb-0">Total Karyawan</p>
                        </div>
                        <div class="col-6">
                            <h2 class="display-5 mb-2">{{ $totalEmployees - $performanceStats['no_rating'] }}</h2>
                            <p class="text-muted mb-0">Sudah Dinilai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Kinerja -->
        <div class="col-md-8 mb-3 mb-md-0">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-header-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>
                        Distribusi Kinerja Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kinerja Detail -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Statistik Kinerja
            </h5>
        </div>
        <div class="card-body">
            <!-- Desktop View -->
            <div class="row g-3 d-none d-lg-flex">
                <div class="col">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Sangat Baik</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['excellent'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['excellent'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-info text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Baik</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['good'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['good'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Cukup</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['average'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['average'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-warning shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Kurang</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['below_average'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['below_average'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-danger text-white shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Sangat Kurang</h5>
                            <h2 class="display-6 mb-2">{{ $performanceStats['poor'] }}</h2>
                            <p class="mb-0">{{ $totalEmployees > 0 ? round(($performanceStats['poor'] / $totalEmployees) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile/Tablet View -->
            <div class="row g-3 d-lg-none">
                <div class="col-6 col-md-4">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Sangat Baik</h6>
                            <h3 class="mb-2">{{ $performanceStats['excellent'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['excellent'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Baik</h6>
                            <h3 class="mb-2">{{ $performanceStats['good'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['good'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Cukup</h6>
                            <h3 class="mb-2">{{ $performanceStats['average'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['average'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-warning shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Kurang</h6>
                            <h3 class="mb-2">{{ $performanceStats['below_average'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['below_average'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body text-center py-3">
                            <h6 class="mb-2">Sangat Kurang</h6>
                            <h3 class="mb-2">{{ $performanceStats['poor'] }}</h3>
                            <small>{{ $totalEmployees > 0 ? round(($performanceStats['poor'] / $totalEmployees) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Perkembangan Kinerja Divisi -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-header-title mb-0">
                <i class="fas fa-chart-line me-2 text-success"></i>
                Perkembangan Kinerja Divisi
            </h5>
        </div>
        <div class="card-body">
            @if (count($divisionMonthlyPerformance) > 0)
                <div class="chart-container">
                    <canvas id="divisionPerformanceChart"></canvas>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center m-0" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0">Belum ada data kinerja divisi</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Detect if mobile
        const isMobile = window.innerWidth < 768;

        // Data distribusi kinerja
        const performanceStats = @json($performanceStats);
        const ctxDistribution = document.getElementById('performanceDistribution').getContext('2d');

        new Chart(ctxDistribution, {
            type: 'pie',
            data: {
                labels: ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang', 'Belum Dinilai'],
                datasets: [{
                    data: [
                        performanceStats.excellent,
                        performanceStats.good,
                        performanceStats.average,
                        performanceStats.below_average,
                        performanceStats.poor,
                        performanceStats.no_rating
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgb(40, 167, 69)',
                        'rgb(23, 162, 184)',
                        'rgb(0, 123, 255)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(108, 117, 125)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'right',
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

        // Data kinerja divisi bulanan
        const divisionData = @json($divisionMonthlyPerformance);

        if (divisionData.length > 0) {
            const ctxDivision = document.getElementById('divisionPerformanceChart').getContext('2d');

            const labels = divisionData.map(item => item.period);
            const avgScores = divisionData.map(item => item.average_score);
            const totalTasks = divisionData.map(item => item.total_tasks);
            const totalEmployees = divisionData.map(item => item.total_employees);

            new Chart(ctxDivision, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Nilai Rata-rata Divisi',
                            data: avgScores,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            yAxisID: 'y',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Jumlah Tugas',
                            data: totalTasks,
                            borderColor: 'rgb(153, 102, 255)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            yAxisID: 'y1',
                            type: 'bar'
                        },
                        {
                            label: 'Karyawan Aktif',
                            data: totalEmployees,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            yAxisID: 'y2',
                            type: 'bar'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            min: 1,
                            max: 4,
                            title: {
                                display: !isMobile,
                                text: 'Nilai Kinerja',
                                font: {
                                    size: isMobile ? 10 : 12
                                }
                            },
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 0,
                            title: {
                                display: !isMobile,
                                text: 'Jumlah Tugas',
                                font: {
                                    size: isMobile ? 10 : 12
                                }
                            },
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: !isMobile,
                            position: 'right',
                            min: 0,
                            title: {
                                display: true,
                                text: 'Karyawan Aktif',
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                drawOnChartArea: false
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
        }
    });
</script>
@endpush
@endsection