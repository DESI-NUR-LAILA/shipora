@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-eye mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-info">
            <a href="{{ route('cuti') }}" class="btn btn-sm btn-light text-info">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 35%;">Nama Pegawai</th>
                        <td class="font-weight-semibold">{{ $cuti->pegawai->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">ID Card</th>
                        <td>{{ $cuti->pegawai->id_card }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bagian</th>
                        <td>{{ $cuti->pegawai->bagian }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Penempatan</th>
                        <td>{{ $cuti->pegawai->port->port }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jenis Pekerjaan</th>
                        <td>{{ $cuti->pegawai->jenis_pekerjaan }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Asal</th>
                        <td>{{ $cuti->pegawai->asal }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tujuan</th>
                        <td>{{ $cuti->tujuan }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Mulai</th>
                        <td>
                            <i class="fas fa-calendar-day text-primary mr-1"></i>
                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Selesai</th>
                        <td>
                            <i class="fas fa-calendar-check text-danger mr-1"></i>
                            {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Lama Cuti</th>
                        <td>
                            <span class="badge badge-primary px-3 py-2">
                                {{ $cuti->lama_hari }} Hari Kerja, {{ $cuti->hari_libur }} Hari Libur
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Sisa Hak Cuti</th>
                        <td>
                            <span class="badge badge-danger px-3 py-2">
                                {{ $cuti->sisa_hak_cuti }} Hari
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Keperluan</th>
                        <td>{{ $cuti->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Berkendaraan</th>
                        <td>{{ $cuti->berkendaraan ?? '-' }}</td>
                    </tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @switch($cuti->status)
                                @case('pending')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                    @break
                               @case('diketahui')
                                    <span class="badge badge-primary">
                                        <i class="fas fa-check"></i> Diketahui
                                    </span>
                                    @break

                                @case('disetujui')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Disetujui
                                    </span>
                                    @break

                                @case('ditolak')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> Ditolak
                                    </span>
                                    @break

                                @default
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-question-circle"></i> Tidak Diketahui
                                    </span>
                            @endswitch
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </div>
@endsection