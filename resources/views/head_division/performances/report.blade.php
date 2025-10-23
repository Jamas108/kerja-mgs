@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Laporan Kinerja Divisi') }}</span>
                        <div>
                            <a href="{{ route('head_division.performances.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button class="btn btn-sm btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Karyawan Divisi</h5>
                                    <div class="d-flex justify-content-between mt-3">
                                        <div>
                                            <h2 class="display-4">{{ $totalEmployees }}</h2>
                                            <p class="text-muted">Total Karyawan</p>
                                        </div>
                                        <div>
                                            <h2 class="display-4">{{ $totalEmployees - $performanceStats['no_rating'] }}</h2>
                                            <p class="text-muted">Sudah Dinilai</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Distribusi Kinerja Karyawan</h5>
                                    <canvas id="performanceDistribution" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Statistik Kinerja</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5>Sangat Baik</h5>
                                            <h2>{{ $performanceStats['excellent'] }}</h2>
                                            <p>{{ $totalEmployees > 0 ? round(($performanceStats['excellent'] / $totalEmployees) * 100) : 0 }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5>Baik</h5>
                                            <h2>{{ $performanceStats['good'] }}</h2>
                                            <p>{{ $totalEmployees > 0 ? round(($performanceStats['good'] / $totalEmployees) * 100) : 0 }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5>Cukup</h5>
                                            <h2>{{ $performanceStats['average'] }}</h2>
                                            <p>{{ $totalEmployees > 0 ? round(($performanceStats['average'] / $totalEmployees) * 100) : 0 }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card bg-warning">
                                        <div class="card-body text-center">
                                            <h5>Kurang</h5>
                                            <h2>{{ $performanceStats['below_average'] }}</h2>
                                            <p>{{ $totalEmployees > 0 ? round(($performanceStats['below_average'] / $totalEmployees) * 100) : 0 }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h5>Sangat Kurang</h5>
                                            <h2>{{ $performanceStats['poor'] }}</h2>
                                            <p>{{ $totalEmployees > 0 ? round(($performanceStats['poor'] / $totalEmployees) * 100) : 0 }}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Perkembangan Kinerja Divisi</h5>
                        </div>
                        <div class="card-body">
                            @if (count($divisionMonthlyPerformance) > 0)
                                <canvas id="divisionPerformanceChart" height="300"></canvas>
                            @else
                                <p class="text-center text-muted my-5">Belum ada data kinerja divisi</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                plugins: {
                    legend: {
                        position: 'right',
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
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            min: 1,
                            max: 4,
                            title: {
                                display: true,
                                text: 'Nilai Kinerja'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 0,
                            title: {
                                display: true,
                                text: 'Jumlah Tugas'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 0,
                            title: {
                                display: true,
                                text: 'Karyawan Aktif'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<style>
    @media print {
        .btn, .card-header {
            display: none !important;
        }

        .container {
            width: 100% !important;
            max-width: 100% !important;
        }

        body {
            padding: 20px !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        h1:first-of-type {
            margin-top: 0 !important;
        }
    }
</style>
@endsection