@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-eye mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('pergerakan') }}" class="btn btn-sm btn-info text-light">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>

            <div class="mb-1 mr-2">
                <!-- Export Excel -->
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>

                    <div class="dropdown-menu p-3" style="min-width: 220px;">
                        <h6 class="dropdown-header mb-2 text-center">Export Excel Per Bulan</h6>

                        <form action="{{ route('pergerakanExcel') }}" method="GET">
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

                        <form action="{{ route('pergerakanPdf') }}" method="GET">
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
            <style>
                .table-nowrap td,
                .table-nowrap th {
                    white-space: nowrap !important;
                    vertical-align: middle;
                }

                .table-responsive {
                    overflow-x: auto;
                }
            </style>
             <div class="table-responsive">
                <table class="table table-bordered table-hover table-nowrap" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-info text-white">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Ship Name</th>
                            <th>GRT</th>
                            <th>DWT</th>
                            <th>Flag</th>
                            <th>Principal</th>
                            <th>ATA</th>
                            <th>Last Port</th>
                            <th>ATD</th>
                            <th>Next Port</th>
                            <th>Activities</th>
                            <th>Jetty</th>
                            <th>Cargo (MT)</th>
                            <th>Status</th>
                            @if(auth()->user()->hak_akses && !in_array(auth()->user()->hak_akses->nama_hak_akses, ['HOA', 'Supervisor']))
                                <th><i class="fas fa-cog"></i></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pergerakan as $item)
                            <tr class="align-middle">
                                <!-- No -->
                                <td class="text-center">
                                    <span class="font-weight-bold">{{ $loop->iteration }}</span>
                                </td>

                                <!-- Data Pergerakan -->
                                <td>{{ $item->ship_name }}</td>
                                <td class="text-center">{{ $item->grt }}</td>
                                <td class="text-center">{{ $item->dwt ?? '-' }}</td>
                                <td>{{ $item->flag }}</td>
                                <td>{{ $item->principal }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->ata)->format('d-m-Y') }}</td>
                                <td>{{ $item->last_port }}</td>
                                <td class="text-center">{{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d-m-Y') : '-' }}</td>
                                <td>{{ $item->next_port ?? '-' }}</td>
                                <td>{{ $item->activities ?? '-' }}</td>
                                <td>{{ $item->jetty ?? '-' }}</td>
                                <td>{{ $item->cargo ?? '-' }}</td>
                                <!-- Status -->
                                <td class="text-center">
                                    @if($item->status === 'CMP')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-success" style="font-size: 14px;">
                                            CMP
                                        </span>
                                    @elseif($item->status === 'Pihak Ketiga')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-danger" style="font-size: 14px;">
                                            Pihak Ketiga
                                        </span>
                                    @elseif($item->status === 'Tugboat')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-warning" style="font-size: 14px;">
                                            Tugboat
                                        </span>
                                    @else
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-secondary" style="font-size: 14px;">
                                            -
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi hanya muncul kalau bukan HOA dan Supervisor --}}
                                @if(auth()->user()->hak_akses && !in_array(auth()->user()->hak_akses->nama_hak_akses, ['HOA', 'Supervisor']))
                                    <td class="text-center">
                                        @php
                                            $hak_akses = auth()->user()->hak_akses->nama_hak_akses ?? '';
                                        @endphp

                                        @if($hak_akses === 'Admin' || $hak_akses === 'Sekretaris')
                                            {{-- Tombol Edit (boleh untuk Admin & Sekretaris) --}}
                                            <a href="{{ route('pergerakanEdit', ['id' => $item->id, 'origin' => 'detail']) }}" 
                                                class="btn btn-sm btn-warning shadow-sm" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection