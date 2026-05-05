@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-anchor mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('portCreate') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
                </a>
            </div>
            <div>  
                <a href="{{ route('portExcel') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-2"></i>
                    Excel
                </a>
                <a href="{{ route('portPdf') }}" class="btn btn-sm btn-danger" target='__blank'>
                    <i class="fas fa-file-pdf mr-2"></i>
                    PDF
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Lokasi Port</th>
                            <th>
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($port as $item)
                            <tr class="align-middle">
                                <!-- No -->
                                <td class="text-center">{{ $loop->iteration }} </td>
                                
                                <!-- Lokasi Port -->
                                <td class="text-center">{{ $item->port }}</td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <!-- Edit -->
                                    <a href="{{ route('portEdit', $item->id) }}" 
                                        class="btn btn-sm btn-warning shadow-sm" 
                                        title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    <em>Belum ada data lokasi port</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection