@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            {{-- Tombol Tambah Data hanya untuk Admin --}}
            @if(auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses === 'Admin')
                <div class="mb-1 mr-2">
                    <a href="{{ route('pegawaiCreate') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Data
                    </a>
                </div>
            @endif

            {{-- Filter Berdasarkan Admin hanya untuk Sekretaris --}}
             @if(auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris','HOA', 'Supervisor']))
                <div class="mb-1 mr-2">
                    <form action="{{ route('pegawai') }}" method="GET" class="form-inline">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-primary text-primary">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                            <select name="port_id"
                                    class="form-control bg-primary text-white border-primary font-weight-bold"
                                    style="cursor: pointer;"
                                    onchange="this.form.submit()">
                                <option value="all" {{ request('port_id') === 'all' ? 'selected' : '' }}>-- Semua Port --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->port_id }}"
                                        {{ request('port_id') == $admin->port_id ? 'selected' : '' }}>
                                        {{ $admin->port->port ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif

            <div>  
                <a href="{{ route('pegawaiExcel', ['port_id' => request('port_id')]) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-2"></i> Excel
                </a>
                <a href="{{ route('pegawaiPdf', ['port_id' => request('port_id')]) }}" class="btn btn-sm btn-danger" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </a>
                @if(auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses === 'Admin')
                    <a href="{{ route('pegawaiArsip') }}" class="btn btn-sm btn-dark">
                        <i class="fas fa-archive mr-2"></i>
                        Lihat Arsip
                    </a>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th>No</th>
                            <th>ID Card</th>
                            <th>Nama</th>
                            <th>Asal</th>
                            <th>Tanda Tangan</th>
                            @if(auth()->user()->hak_akses && !in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor']))
                                <th>
                                    <i class="fas fa-cog"></i>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawai as $item)
                            <tr class="align-middle">
                                <!-- No -->
                                <td class="text-center">{{ $loop->iteration }}</td>

                                <!-- ID Card -->
                                <td>{{ $item->id_card }}</td>

                                <!-- Nama -->
                                <td>{{ $item->nama }}</td>

                                <!-- Asal -->
                                <td>{{ $item->asal }}</td>

                                <!-- Tanda Tangan -->
                                <td class="text-center">
                                    <img src="{{ asset('storage/' . $item->ttd_path) }}" alt="TTD" height="40">
                                </td>

                                <!-- Aksi -->
                                {{-- Kolom Aksi hanya muncul kalau bukan HOA dan Supervisor --}}
                                @if(auth()->user()->hak_akses && !in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor']))
                                    <td class="text-center">
                                        {{-- Detail --}}
                                        <a href="{{ route('pegawaiDetail', ['id' => $item->id, 'origin' => 'pegawai']) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @php
                                            $hak_akses = auth()->user()->hak_akses->nama_hak_akses ?? '';
                                        @endphp

                                        @if($hak_akses === 'Admin')
                                            <!-- Edit -->
                                            <a href="{{ route('pegawaiEdit', $item->id) }}"  
                                                class="btn btn-sm btn-warning shadow-sm" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if (!$item->isarsip)
                                            <button type="button"
                                                class="btn btn-sm btn-secondary shadow-sm"
                                                title="Arsipkan Data"
                                                data-toggle="modal"
                                                data-target="#arsipModal{{ $item->id }}">
                                                <i class="fas fa-archive"></i>
                                            </button>

                                            {{-- Modal Arsip --}}
                                            <div class="modal fade" id="arsipModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="arsipModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-secondary text-white">
                                                            <h5 class="modal-title">Konfirmasi Arsip</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <p>Apakah Anda yakin ingin memindahkan data ini ke arsip?</p>
                                                            <div class="border rounded p-3 bg-light mt-2">
                                                                <p class="mb-1"><strong>ID Card:</strong> {{ $item->id_card ?? '-' }}</p>
                                                                <p class="mb-1"><strong>Nama:</strong> {{ $item->nama ?? '-' }}</p>
                                                                <p class="mb-1"><strong>Asal:</strong> {{ $item->asal ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                                                <i class="fas fa-times"></i> Tutup
                                                            </button>
                                                            <form action="{{ route('pegawaiArsipkan', $item->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-archive"></i> Ya, Arsipkan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary">Diarsipkan</span>
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