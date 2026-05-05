<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pergerakan Kapal</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}">
</head>
<body>

    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN PERGERAKAN KAPAL</h2>
        <h4>DI PELABUHAN {{ strtoupper($nama_pelabuhan) }}</h4>
        <h4>PERIODE BULAN {{ strtoupper($periode) }}</h4>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Kapal</th>
                <th colspan="2">Data Kapal</th>
                <th rowspan="2">Prinsipal</th>
                <th colspan="2">Datang</th>
                <th colspan="2">Berangkat</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>DWT</th>
                <th>GRT</th>
                <th>Tanggal</th>
                <th>Dari</th>
                <th>Tanggal</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pergerakan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->ship_name }}</td>
                    <td class="text-center">{{ $item->dwt ?? '-' }}</td>
                    <td class="text-center">{{ $item->grt ?? '-' }}</td>
                    <td>{{ $item->principal ?? '-' }}</td>
                    <td class="text-center">{{ $item->ata ? \Carbon\Carbon::parse($item->ata)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->last_port ?? '-' }}</td>
                    <td class="text-center">{{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->next_port ?? '-' }}</td>
                    <td>{{ $item->status ?? '_' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Tidak ada data pergerakan kapal.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer tanda tangan -->
    <table class="footer">
        <tr>
            <td width="70%"></td>
            <td>
                {{ str($nama_pelabuhan) }}, {{ $tanggal_akhir_display }}<br>
                <strong>PT Pertamina Trans Kontinental</strong>
                <div style="text-align:center;">
                    @if($ttd_pegawai)
                        <img src="{{ public_path('storage/' . $ttd_pegawai) }}" 
                            alt="TTD" 
                            style="width:300px; display:block; margin:5px auto 0 auto;">
                    @endif
                    <u style="display:block; margin-top:-30px;">{{ $nama_pegawai }}</u>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>