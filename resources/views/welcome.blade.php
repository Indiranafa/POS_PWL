@extends('layouts.template')

@section('content')
<div class="row pl-3 pr-3">
    <!-- Card 1 - Total Pengguna -->
    <div class="col-lg-3 col-6 mb-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalPengguna }}</h3>
                <p>Total Pengguna</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ url('/user') }}" class="small-box-footer">
                Selengkapnya <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card 2 - Total Barang -->
    <div class="col-lg-3 col-6 mb-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalBarang }}</h3>
                <p>Total Barang</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="{{ url('/barang') }}" class="small-box-footer">
                Selengkapnya <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card 3 - Total Stok -->
    <div class="col-lg-3 col-6 mb-4">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalStok }}</h3>
                <p>Total Stok</p>
            </div>
            <div class="icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <a href="{{ url('/stok') }}" class="small-box-footer">
                Selengkapnya <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card 4 - Total Penjualan -->
    <div class="col-lg-3 col-6 mb-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                <p>Total Penjualan</p>
            </div>
            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
            <a href="{{ url('/penjualan') }}" class="small-box-footer">
                Selengkapnya <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Grafik Penjualan -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-line"></i> Statistik Penjualan (Per Bulan)
        </h3>
    </div>
    <div class="card-body">
        <canvas id="penjualanChart" width="800" height="200"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('penjualanChart').getContext('2d');
        var penjualanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labelsBulan),
                datasets: [{
                    label: 'Total Penjualan per Bulan (Rp)',
                    data: @json($dataPenjualan),
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    });
</script>
@endpush
