<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pergerakan Kapal</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}"
</head>
<body>

    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN KEDATANGAN DAN KEBERANGKATAN KAPAL</h2>
        <h4>DI PELABUHAN {{ strtoupper($nama_pelabuhan) }}</h4>
        <h4>PERIODE BULAN {{ strtoupper($periode) }}</h4>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>SHIP NAME</th>
                <th>GRT</th>
                <th>DWT</th>
                <th>FLAG</th>
                <th>PRINCIPAL</th>
                <th>ATA</th>
                <th>LAST PORT</th>
                <th>ATD</th>
                <th>NEXT PORT</th>
                <th>ACTIVITIES</th>
                <th>JETTY</th>
                <th>CARGO (MT)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pergerakan as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->ship_name }}</td>
                <td class="text-center">{{ $item->grt }}</td>
                <td class="text-center">{{ $item->dwt }}</td>
                <td>{{ $item->flag }}</td>
                <td>{{ $item->principal }}</td>
                <td class="text-center">
                    {{ $item->ata ? \Carbon\Carbon::parse($item->ata)->format('d/m/Y') : '-' }}
                </td>
                <td>{{ $item->last_port ?? '-' }}</td>
                <td class="text-center">
                    {{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d/m/Y') : '-' }}
                </td>
                <td>{{ $item->next_port ?? '-' }}</td>
                <td>{{ $item->activities ?? '-' }}</td>
                <td>{{ $item->jetty ?? '-' }}</td>
                <td>{{ $item->cargo ?? '-' }}</td>
            </tr>
            @endforeach
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
