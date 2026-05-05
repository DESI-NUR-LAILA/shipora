<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SOD</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}"
</head>
<body>

    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN SOD</h2>
        <h4>DI PELABUHAN {{ strtoupper($nama_pelabuhan) }}</h4>
        <h4>PERIODE BULAN {{ strtoupper($periode) }}</h4>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>SHIP NAME</th>
                <th>STATUS KAPAL</th>
                <th>ATA</th>
                <th>ATD</th>
                <th>LAST PORT</th>
                <th>NEXT PORT</th>
                <th>STATUS LAPORAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->pergerakan->ship_name }}</td>
                <td class="text-center">{{ $item->pergerakan->status }}</td>
                <td class="text-center">
                    {{ $item->pergerakan->ata ? \Carbon\Carbon::parse($item->ata)->format('d/m/Y') : '-' }}
                </td>
                <td class="text-center">
                    {{ $item->pergerakan->atd ? \Carbon\Carbon::parse($item->atd)->format('d/m/Y') : '-' }}
                </td>
                <td class="text-center">{{ $item->pergerakan->last_port ?? '-' }}</td>
                <td class="text-center">{{ $item->pergerakan->next_port ?? '-' }}</td>
                <td class="text-center">{{ $item->status ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer tanda tangan -->
    <table class="footer">
        <tr>
            <td width="70%"></td>
            <td>
                {{ strtoupper($nama_pelabuhan) }}, {{ $tanggal_akhir_display }}<br>
                <strong>PT Pertamina Trans Kontinental</strong>
                <div style="text-align:center;">
                    @if($ttd_pegawai)
                        <img src="{{ public_path('storage/' . $ttd_pegawai) }}" 
                            alt="TTD" 
                            style="width:300px; display:block; margin:5px auto 0 auto; vertical-align:bottom;">
                    @endif
                    <u style="display:block; margin-top:-30px;">{{ $nama_pegawai }}</u>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>