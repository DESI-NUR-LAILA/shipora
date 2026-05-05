@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-file-contract mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('iogCreate') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
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
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Nama Pegawai</th>
                            <th>Nama Kapal</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($iog as $item)
                            <tr class="align-middle">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_surat }}</td>
                                <td>{{ $item->pegawai->nama }}</td>
                                <td>{{ $item->nama_kapal }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Detail -->
                                        <a href="{{ route('iogDetail', $item->id) }}" 
                                        class="btn btn-sm btn-info shadow-sm" 
                                        title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Edit -->
                                        <a href="{{ route('iogEdit', $item->id) }}" 
                                        class="btn btn-sm btn-warning shadow-sm" 
                                        title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- pdf -->
                                        <a href="{{ route('iogPdf', $item->id) }}" 
                                        class="btn btn-sm bg-white text-danger border border-danger shadow-sm" 
                                        title="Cetak PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <em>Belum ada pengajuan ijin olah gerak</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection