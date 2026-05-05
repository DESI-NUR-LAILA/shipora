@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-ship mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            {{-- Tombol Tambah Data hanya untuk Admin --}}
            @if(auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses === 'Admin')
                <div class="mb-1 mr-2">
                    <a href="{{ route('pergerakanCreate') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Data
                    </a>
                </div>
            @endif

            {{-- Filter Berdasarkan Admin hanya untuk Sekretaris --}}
            @if(auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris','HOA', 'Supervisor']))
                <div class="mb-1 mr-2">
                    <form action="{{ route('pergerakan') }}" method="GET" class="form-inline">
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
                <a href="{{ route('pergerakanDetail', ['user_id' => request('user_id')]) }}" 
                class="btn btn-sm btn-info">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                </a>
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
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Nama Kapal</th>
                            <th colspan="2">Data Kapal</th>
                            <th rowspan="2" class="align-middle">Prinsipal</th>
                            <th colspan="2">Datang</th>
                            <th colspan="2">Berangkat</th>
                            <th rowspan="2" class="align-middle">Status</th>
                            @if(auth()->user()->hak_akses && !in_array(auth()->user()->hak_akses->nama_hak_akses, ['HOA', 'Supervisor']))
                                <th rowspan="2" class="align-middle"><i class="fas fa-cog"></i></th>
                            @endif
                        </tr>
                        <tr class="text-center">
                            <th>DWT</th>
                            <th>GRT</th>
                            <th>Tanggal</th>
                            <th>Dari</th>
                            <th>Tanggal</th>
                            <th>Tujuan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pergerakan as $item)
                            <tr class="align-middle">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->ship_name }}</td>
                                <td class="text-center">{{ $item->dwt ?? '-' }}</td>
                                <td class="text-center">{{ $item->grt }}</td>
                                <td>{{ $item->principal }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->ata)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $item->last_port }}</td>
                                <td class="text-center">{{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d-m-Y') : '-' }}</td>
                                <td class="text-center">{{ $item->next_port ?? '-' }}</td>

                                <td class="text-center">
                                    @if($item->status === 'CMP')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-success" style="font-size: 14px;">CMP</span>
                                    @elseif($item->status === 'Pihak Ketiga')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-danger" style="font-size: 14px;">Pihak Ketiga</span>
                                    @elseif($item->status === 'Tugboat')
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-warning" style="font-size: 14px;">Tugboat</span>
                                    @else
                                        <span class="badge badge-light border px-3 py-2 shadow-sm text-secondary" style="font-size: 14px;">-</span>
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
                                            <a href="{{ route('pergerakanEdit', ['id' => $item->id, 'origin' => 'index']) }}" 
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