@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-tachometer-alt mr-2"></i>
        {{ $title }}
    </h1>

    @if (auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses == 'Sekretaris')

        <div class="row">
            {{-- Total Lokasi Port --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('portPdf') }}" target="_blank" style="text-decoration: none;">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Lokasi Port
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPort }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Total User --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('userPdf', ['role' => 'all']) }}" target="_blank" style="text-decoration:none;">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total User
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahUser }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Total Admin --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('userPdf', ['role' => 'admin']) }}" target="_blank" style="text-decoration:none;">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Admin
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahAdmin }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Total Pergerakan Kapal --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('pergerakanKedatangan') }}" target="_blank" style="text-decoration: none;">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Pergerakan Kapal
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPergerakan }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ship fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Laporan --}}
        <div class="row">
            {{-- Chart Laporan --}}
            <div class="col-xl-12 col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Laporan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="laporanChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram Trend --}}
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        Trend Dokumen per Status Kapal Tahun {{ $tahun }}
                    </h6>
                </div>

                <div class="card-body">

                    {{-- ✅ FILTER DI SINI --}}
                    <form method="GET" class="mb-3" id="filterForm">
                        <div style="display:flex; justify-content:space-between; align-items:center;">

                            {{-- KIRI: FILTER --}}
                            <div style="display:flex; gap:10px; align-items:center;">
                                <label>Pilih Tahun:</label>

                                <select name="tahun" class="form-control" style="width:150px;" 
                                    onchange="document.getElementById('filterForm').submit();">

                                    @for ($i = date('Y'); $i >= 2025; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            {{-- KANAN: TOMBOL PDF --}}
                            <div>
                                <a href="{{ route('dashboardPdf', ['tahun' => $tahun]) }}" 
                                target="_blank" 
                                class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                            </div>

                        </div>
                    </form>

                    {{-- CHART --}}
                    <canvas id="trendChart" height="100"></canvas>

                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses == 'Admin')
        <div class="row">
            {{-- Total Pegawai --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('pegawaiPdf') }}" target="_blank" style="text-decoration:none;">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Pegawai
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPegawai }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            {{-- Total Surat Cuti --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Surat Cuti
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $jumlahCuti }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pergerakan Kapal --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('pergerakanKedatangan') }}" target="_blank" style="text-decoration: none;">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Pergerakan Kapal
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPergerakan }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ship fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div> 

        {{-- Chart Surat Cuti $$ Laporan --}}
        <div class="row">
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Surat Cuti</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="cutiChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Laporan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="laporanChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram Trend --}}
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        Trend Dokumen per Status Kapal Tahun {{ $tahun }}
                    </h6>
                </div>

                <div class="card-body">

                    {{-- ✅ FILTER DI SINI --}}
                    <form method="GET" class="mb-3" id="filterForm">
                        <div style="display:flex; justify-content:space-between; align-items:center;">

                            {{-- KIRI: FILTER --}}
                            <div style="display:flex; gap:10px; align-items:center;">
                                <label>Pilih Tahun:</label>

                                <select name="tahun" class="form-control" style="width:150px;" 
                                    onchange="document.getElementById('filterForm').submit();">

                                    @for ($i = date('Y'); $i >= 2025; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            {{-- KANAN: TOMBOL PDF --}}
                            <div>
                                <a href="{{ route('dashboardPdf', ['tahun' => $tahun]) }}" 
                                target="_blank" 
                                class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                            </div>

                        </div>
                    </form>

                    {{-- CHART --}}
                    <canvas id="trendChart" height="100"></canvas>

                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses == 'HOA')
        <div class="row">
            {{-- Total Pergerakan Kapal --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('pergerakanKedatangan') }}" target="_blank" style="text-decoration: none;">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Pergerakan Kapal
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPergerakan }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ship fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Laporan --}}
        <div class="row">
            {{-- Chart Laporan --}}
            <div class="col-xl-12 col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Laporan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="laporanChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram Trend --}}
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        Trend Dokumen per Status Kapal Tahun {{ $tahun }}
                    </h6>
                </div>

                <div class="card-body">

                    {{-- ✅ FILTER DI SINI --}}
                    <form method="GET" class="mb-3" id="filterForm">
                        <div style="display:flex; justify-content:space-between; align-items:center;">

                            {{-- KIRI: FILTER --}}
                            <div style="display:flex; gap:10px; align-items:center;">
                                <label>Pilih Tahun:</label>

                                <select name="tahun" class="form-control" style="width:150px;" 
                                    onchange="document.getElementById('filterForm').submit();">

                                    @for ($i = date('Y'); $i >= 2025; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            {{-- KANAN: TOMBOL PDF --}}
                            <div>
                                <a href="{{ route('dashboardPdf', ['tahun' => $tahun]) }}" 
                                target="_blank" 
                                class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                            </div>

                        </div>
                    </form>

                    {{-- CHART --}}
                    <canvas id="trendChart" height="100"></canvas>

                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses == 'Supervisor')
        <div class="row">
            {{-- Total Surat Cuti --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Surat Cuti
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $jumlahCuti }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pergerakan Kapal --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('pergerakanKedatangan') }}" target="_blank" style="text-decoration: none;">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Pergerakan Kapal
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $jumlahPergerakan }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ship fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Chart Surat Cuti $$ Laporan --}}
        <div class="row">
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Surat Cuti</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="cutiChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Laporan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="laporanChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram Trend --}}
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        Trend Dokumen per Status Kapal Tahun {{ $tahun }}
                    </h6>
                </div>

                <div class="card-body">

                    {{-- ✅ FILTER DI SINI --}}
                    <form method="GET" class="mb-3" id="filterForm">
                        <div style="display:flex; justify-content:space-between; align-items:center;">

                            {{-- KIRI: FILTER --}}
                            <div style="display:flex; gap:10px; align-items:center;">
                                <label>Pilih Tahun:</label>

                                <select name="tahun" class="form-control" style="width:150px;" 
                                    onchange="document.getElementById('filterForm').submit();">

                                    @for ($i = date('Y'); $i >= 2025; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            {{-- KANAN: TOMBOL PDF --}}
                            <div>
                                <a href="{{ route('dashboardPdf', ['tahun' => $tahun]) }}" 
                                target="_blank" 
                                class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                            </div>

                        </div>
                    </form>

                    {{-- CHART --}}
                    <canvas id="trendChart" height="100"></canvas>

                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses == 'PIC')
        <div class="row">
            {{-- Total Surat Cuti --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Surat Cuti
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $jumlahCuti }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Surat Cuti --}}
        <div class="row">
            {{-- Chart Surat Cuti --}}
            <div class="col-xl-12 col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Surat Cuti</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="cutiChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chart Surat Cuti (hanya jika ada elemen cutiChart)
            const cutiCanvas = document.getElementById('cutiChart');
            if (cutiCanvas) {
                new Chart(cutiCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['pending', 'diketahui', 'disetujui', 'ditolak'],
                        datasets: [{
                            label: 'Jumlah Surat Cuti',
                            data: [
                                {{ $cutiPending ?? 0 }},
                                {{ $cutiDiketahui ?? 0 }},
                                {{ $cutiDisetujui ?? 0 }},
                                {{ $cutiDitolak ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(108, 117, 125, 0.7)',
                                'rgba(0, 123, 255, 0.7)',
                                'rgba(40, 167, 69, 0.7)',
                                'rgba(220, 53, 69, 0.7)'
                            ],
                            borderColor: [
                                'rgba(108, 117, 125, 1)',
                                'rgba(0, 123, 255, 1)',
                                'rgba(40, 167, 69, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, stepSize: 1 } }
                    }
                });
            }

            // Chart Laporan (hanya jika ada elemen laporanChart)
            const laporanCanvas = document.getElementById('laporanChart');
            if (laporanCanvas) {
                new Chart(laporanCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['draft', 'dikirim', 'disetujui', 'ditolak'],
                        datasets: [{
                            label: 'Jumlah Laporan',
                            data: [
                                {{ $laporanDraft ?? 0 }},
                                {{ $laporanDikirim ?? 0 }},
                                {{ $laporanDisetujui ?? 0 }},
                                {{ $laporanDitolak ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(108, 117, 125, 0.7)',
                                'rgba(0, 123, 255, 0.7)',
                                'rgba(40, 167, 69, 0.7)',
                                'rgba(220, 53, 69, 0.7)'
                            ],
                            borderColor: [
                                'rgba(108, 117, 125, 1)',
                                'rgba(0, 123, 255, 1)',
                                'rgba(40, 167, 69, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, stepSize: 1 } }
                    }
                });
            }
        });

        // ==============================
        // CHART TREND DOKUMEN
        // ==============================

        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            new Chart(trendCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'CMP',
                            data: @json($chartCMP),
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Pihak Ketiga',
                            data: @json($chartPihakKetiga),
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Tugboat',
                            data: @json($chartTugboat),
                            borderWidth: 2,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endpush