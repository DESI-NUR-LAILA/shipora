@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-eye mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-info">
            <a href="{{ $origin === 'arsip' ? route('pegawaiArsip') : route('pegawai') }}" class="btn btn-sm btn-light text-info">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted">ID Card</th>
                        <td class="font-weight-semibold">{{ $pegawai->id_card }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted" style="width: 35%;">Nama Pegawai</th>
                        <td class="font-weight-semibold">{{ $pegawai->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bagian</th>
                        <td>{{ $pegawai->bagian }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Penempatan</th>
                        <td>{{ $pegawai->port->port }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jenis Pekerjaan</th>
                        <td>{{ $pegawai->jenis_pekerjaan }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Asal</th>
                        <td>{{ $pegawai->asal }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanda Tangan</th>
                        <td>
                            @if($pegawai->ttd_path)
                                <div style="display: inline-block; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;">
                                    <img 
                                        src="{{ asset('storage/' . $pegawai->ttd_path) }}" 
                                        alt="TTD" 
                                        style="max-height: 80px; max-width: 200px; object-fit: contain;"
                                    >
                                </div>
                            @else
                                <span class="text-muted">Belum ada tanda tangan</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection