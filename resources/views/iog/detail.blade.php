@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-eye mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-info">
            <a href="{{ route('iog') }}" class="btn btn-sm btn-light text-info">
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
                        <th class="text-muted">Nomor Surat</th>
                        <td>{{ $iog->nomor_surat }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Lampiran</th>
                        <td>{{ !empty($iog->lampiran) && $iog->lampiran > 0 ? $iog->lampiran : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted" style="width: 35%;">Nama Pegawai</th>
                        <td class="font-weight-semibold">{{ $iog->pegawai->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nama Kapal</th>
                        <td>{{ $iog->nama_kapal }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Master</th>
                        <td>{{ $iog->master }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bendera</th>
                        <td>{{ $iog->bendera }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">GRT</th>
                        <td>{{ $iog->grt }} GT</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Pemilik</th>
                        <td>{{ $iog->pemilik }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Mulai</th>
                        <td>
                            <i class="fas fa-calendar-day text-primary mr-1"></i>
                            {{ \Carbon\Carbon::parse($iog->tanggal_mulai)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Selesai</th>
                        <td>
                            <i class="fas fa-calendar-check text-danger mr-1"></i>
                            {{ \Carbon\Carbon::parse($iog->tanggal_selesai)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </div>
@endsection