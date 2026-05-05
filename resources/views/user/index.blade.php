@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            <div class="mb-1 mr-2">
                <a href="{{ route('userCreate') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
                </a>
            </div>
            <div>  
                <a href="{{ route('userExcel') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-2"></i>
                    Excel
                </a>
                <a href="{{ route('userPdf', ['role' => 'all']) }}" class="btn btn-sm btn-danger" target='__blank'>
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
                            <th>Email</th>
                            <th>Lokasi Port</th>
                            <th>Hak Akses</th>
                            <th>
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $item)
                            <tr class="align-middle">
                                <!-- No -->
                                <td class="text-center">{{ $loop->iteration }}</td>

                                <!-- Email -->
                                <td>{{ $item->email }}</td>

                                <!-- Lokasi Port -->
                                <td>{{ $item->port?->port ?? '-' }}</td>

                                <!-- Hak Akses -->
                                @php
                                    // Mapping untuk hak akses 
                                    $badgeMap = [
                                        'Admin' => [
                                            'class' => 'dark',
                                            'icon' => 'fas fa-user-tie'
                                        ],
                                        'PIC' => [
                                            'class' => 'info',
                                            'icon' => 'fas fa-user-shield'
                                        ],
                                        'Sekretaris' => [
                                            'class' => 'primary',
                                            'icon' => 'fas fa-user-cog'   // melambangkan pengatur/proyek
                                        ],
                                        'HOA' => [
                                            'class' => 'success',
                                            'icon' => 'fas fa-user-check' // melambangkan otoritas/persetujuan
                                        ],
                                        'Supervisor' => [
                                            'class' => 'warning',
                                            'icon' => 'fas fa-user-tag'   // melambangkan pengawasan
                                        ],
                                    ];

                                    // Default untuk hak akses lain
                                    $defaultBadge = [
                                        'class' => 'light',
                                        'icon' => 'fas fa-user'
                                    ];

                                    // Ambil data hak akses saat ini
                                    $hak_akses = $item->hak_akses?->nama_hak_akses;
                                    $badge = $badgeMap[$hak_akses] ?? $defaultBadge;
                                @endphp

                                <td class="text-center">
                                    <span class="badge badge-{{ $badge['class'] }} px-3 py-2 shadow-sm">
                                        <i class="{{ $badge['icon'] }} mr-1"></i>
                                        {{ $hak_akses ?? '-' }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <!-- Edit -->
                                    <a href="{{ route('userEdit', $item->id) }}" 
                                    class="btn btn-sm btn-warning shadow-sm" 
                                    title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection