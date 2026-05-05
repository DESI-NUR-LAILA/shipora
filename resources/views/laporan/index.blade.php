@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-tasks mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            @if(auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses === 'Admin')
                <div class="mb-1 mr-2">
                        <a href="{{ route('laporanCreate') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Data
                        </a>
                </div>
            @endif

            {{-- Filter Berdasarkan Admin hanya untuk Sekretaris --}}
            @if(auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris','HOA', 'Supervisor']))
                <div class="mb-1 mr-2">
                    <form action="{{ route('laporan') }}" method="GET" class="form-inline">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-primary text-primary">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                            <select name="user_id"
                                class="form-control bg-primary text-white border-primary font-weight-bold"
                                style="cursor: pointer;"
                                onchange="this.form.submit()">
                            <option value="all" {{ request('user_id') == 'all' ? 'selected' : '' }}>-- Semua Port --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" 
                                    {{ request('user_id') == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->port->port ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </form>
                </div>
            @endif

            <div class="mb-1 mr-2">
                <!-- Export Excel -->
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>

                    <div class="dropdown-menu p-3" style="min-width: 220px;">
                        <h6 class="dropdown-header mb-2 text-center">Export Excel Per Bulan</h6>

                        <form action="{{ route('laporanExcel') }}" method="GET">
                            <input type="hidden" name="user_id" value="{{ request('user_id', 'all') }}">
                            <div class="input-group input-group-sm">
                                <input type="month" name="bulan" class="form-control"
                                    value="{{ request('bulan', \Carbon\Carbon::today()->format('Y-m')) }}"
                                    min="2025-09"
                                    max="{{ \Carbon\Carbon::today()->format('Y-m') }}" required>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Export PDF Bulanan -->
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>

                    <div class="dropdown-menu p-3" style="min-width: 220px;">
                        <h6 class="dropdown-header mb-2 text-center">Export PDF Per Bulan</h6>

                        <form action="{{ route('laporanPdf') }}" method="GET">
                            <input type="hidden" name="user_id" value="{{ request('user_id', 'all') }}">
                            <div class="input-group input-group-sm">
                                <input type="month" name="bulan" class="form-control"
                                    value="{{ request('bulan', \Carbon\Carbon::today()->format('Y-m')) }}"
                                    min="2025-09"
                                    max="{{ \Carbon\Carbon::today()->format('Y-m') }}" required>
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Kapal</th>
                            <th>Status Kapal</th>
                            <th>ATA</th>
                            <th>ATD</th>
                            <th>Status</th>
                            <th><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                        <tr class="align-middle">
                            <!-- No -->
                            <td class="text-center">{{ $loop->iteration }}</td>

                            <!-- Nama Kapal -->
                            <td class="text-center">{{ $item->pergerakan?->ship_name ?? '-' }}</td>

                            <!-- Status Kapal-->
                                <td class="text-center">
                                    @if($item->pergerakan?->status === 'CMP')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-success" style="font-size: 14px;">
                                            CMP
                                        </span>
                                    @elseif($item->pergerakan?->status === 'Pihak Ketiga')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-danger" style="font-size: 14px;">
                                            Pihak Ketiga
                                        </span>
                                    @elseif($item->pergerakan?->status === 'Tugboat')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-warning" style="font-size: 14px;">
                                            Tugboat
                                        </span>
                                    @else
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-secondary" style="font-size: 14px;">
                                            -
                                        </span>
                                    @endif
                                </td>

                            <!-- ATA -->
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->pergerakan?->ata)->format('d-m-Y') }}</td>

                            <!-- ATD -->
                            <td class="text-center">
                                {{ $item->pergerakan?->atd ? \Carbon\Carbon::parse($item->pergerakan->atd)->format('d-m-Y') : '-' }}
                            </td>
                            
                            <!-- Status -->
                            <td class="text-center">
                                @php
                                    $statusIcons = [
                                        'draft' => 'fas fa-pencil-alt',
                                        'dikirim' => 'fas fa-paper-plane',
                                        'disetujui' => 'fas fa-check-circle',
                                        'ditolak' => 'fas fa-times-circle',
                                    ];

                                    $badgeColor = match($item->status) {
                                        'draft' => 'secondary',
                                        'dikirim' => 'primary',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'light'
                                    };

                                    $icon = $statusIcons[$item->status] ?? 'fas fa-question-circle';
                                @endphp

                                <span class="badge badge-{{ $badgeColor }}">
                                    <i class="{{ $icon }}"></i> {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('laporanDetail', $item->pergerakan_id) }}" 
                                    class="btn btn-sm btn-info shadow-sm" 
                                    title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Hanya Admin yang bisa Edit & Hapus --}}
                                    @if(Auth::user()->hak_akses->nama_hak_akses === 'Admin')
                                        {{-- Tombol Edit --}}
                                        @if($item->status === 'disetujui')
                                            {{-- Jika laporan sudah disetujui, tidak tampilkan tombol edit --}}
                                            <span></span>
                                        @elseif($item->status === 'draft')
                                            {{-- Langsung menuju halaman edit --}}
                                            <a href="{{ route('laporanEdit', $item->id) }}" 
                                            class="btn btn-sm btn-warning shadow-sm" 
                                            title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            {{-- Tampilkan modal konfirmasi jika status bukan draft --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning shadow-sm" 
                                                    data-toggle="modal" 
                                                    data-target="#modalTidakBisaEdit{{ $item->id }}"
                                                    title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Modal konfirmasi --}}
                                            <div class="modal fade" id="modalTidakBisaEdit{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTidakBisaEditLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-white">
                                                            <h5 class="modal-title" id="modalTidakBisaEditLabel{{ $item->id }}">Konfirmasi Edit Data</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                       <div class="modal-body">
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Laporan <strong>sudah dikirim</strong>.
                                                                Mengedit laporan akan mengubah <strong>status laporan menjadi Draft</strong>.
                                                            </div>
                                                            <p>Apakah Anda yakin ingin melanjutkan proses edit laporan ini?</p>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                                                <i class="fas fa-times"></i> Tutup
                                                            </button>
                                                            <a href="{{ route('laporanEdit', $item->id) }}" class="btn btn-sm btn-outline-warning">
                                                                <i class="fas fa-edit"></i> Lanjutkan Edit
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection