@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-file-signature mr-2"></i> {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
            {{-- Tombol Tambah Data hanya untuk Admin --}}
            @if(auth()->user()->hak_akses && auth()->user()->hak_akses->nama_hak_akses === 'Admin')
                <div class="mb-1 mr-2">
                    <a href="{{ route('cutiCreate') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-2"></i> Tambah Data
                    </a>
                </div>
            @endif

            {{-- Filter Berdasarkan Port untuk Sekretaris, HOA, PIC, Supervisor --}}
            @if(auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'PIC', 'Supervisor']))
                <div class="mb-1 mr-2">
                    <form action="{{ route('cuti') }}" method="GET" class="form-inline">
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
                                <option value="">-- Semua Port --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->port_id }}" {{ request('port_id') == $admin->port_id ? 'selected' : '' }}>
                                        {{ $admin->port->port ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="card-body">
            <style>
                .table-nowrap td, .table-nowrap th {
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
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Sisa Hak Cuti</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cuti as $item)
                            <tr class="align-middle text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $item->pegawai->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}</td>
                                <td>{{ $item->sisa_hak_cuti }}</td>
                                <td class="text-left">{{ $item->keterangan }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('pending')
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                            @break
                                        @case('diketahui')
                                            <span class="badge badge-primary"><i class="fas fa-check"></i> Diketahui</span>
                                            @break
                                        @case('disetujui')
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                                            @break
                                        @case('ditolak')
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                                            {{-- Tampilkan alasan penolakan --}}
                                            @if(!empty($item->alasan_penolakan))
                                                <small class="text-muted d-block mt-1">
                                                    Alasan: {{ $item->alasan_penolakan }}
                                                </small>
                                            @endif
                                            @break
                                        @default
                                            <span class="badge badge-secondary"><i class="fas fa-question-circle"></i> Tidak Diketahui</span>
                                    @endswitch
                                </td>

                                <td>
                                    @php $hak_akses = auth()->user()->hak_akses->nama_hak_akses ?? ''; @endphp
                                    <div class="btn-group" role="group">
                                        <div class="btn-group" role="group">
                                            {{-- Admin - Edit --}}
                                            @if($hak_akses === 'Admin')

                                                {{-- Jika status pending → langsung ke halaman edit --}}
                                                @if($item->status === 'pending')
                                                    <a href="{{ route('cutiEdit', $item->id) }}"
                                                        class="btn btn-warning btn-sm"
                                                        title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                {{-- Jika status diketahui → tampilkan modal konfirmasi --}}
                                                @elseif($item->status === 'diketahui')
                                                    <button class="btn btn-warning btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#modalEdit{{ $item->id }}"
                                                        title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- Supervisor - Mengetahui --}}
                                        @if($hak_akses === 'Supervisor' && $item->status === 'pending')
                                            <button class="btn btn-sm btn-primary shadow-sm"
                                                data-toggle="modal" data-target="#modalMengetahui{{ $item->id }}"
                                                title="Mengetahui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        {{-- PIC --}}
                                        @if($hak_akses === 'PIC' && $item->status === 'diketahui')
                                            {{-- Setujui --}}
                                            <button class="btn btn-sm btn-success shadow-sm"
                                                data-toggle="modal" data-target="#modalMenyetujui{{ $item->id }}"
                                                title="Menyetujui">
                                                <i class="fas fa-check"></i>
                                            </button>

                                            {{-- Tolak --}}
                                            <button class="btn btn-sm btn-danger shadow-sm"
                                                data-toggle="modal" data-target="#modalMenolak{{ $item->id }}"
                                                title="Tolak Pengajuan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        {{-- Detail --}}
                                        <a href="{{ route('cutiDetail', $item->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- PDF --}}
                                        <a href="{{ route('cutiPdf', $item->id) }}" class="btn btn-sm bg-white text-danger border border-danger shadow-sm" target="_blank" title="Cetak PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>

                                    {{-- Modal --}}
                                    @include('cuti/modal', ['aksi' => 'Mengetahui', 'item' => $item])
                                    @include('cuti/modal', ['aksi' => 'Menyetujui', 'item' => $item])
                                    @include('cuti/modal', ['aksi' => 'Menolak', 'item' => $item])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted"><em>Belum ada pengajuan surat cuti</em></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection