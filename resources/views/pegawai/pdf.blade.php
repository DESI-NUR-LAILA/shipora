<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pegawai</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}"
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN DATA PEGAWAI PELABUHAN {{ strtoupper($nama_pelabuhan) }}</h2>
        <h4>PERTAMINA TRANS KONTINENTAL</h4>
        <h4>PERIODE BULAN {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y')) }}</h4>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Card</th>
                <th>Nama</th>
                <th>Asal</th>
                <th>Port</th>
                <th>Jenis Pekerjaan</th>
                <th>Bagian</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawai as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->id_card }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->asal }}</td>
                    <td>{{ $item->port->port }}</td>
                    <td>{{ $item->jenis_pekerjaan }}</td>
                    <td>{{ $item->bagian }}</td>
                    <td class="text-center">
                        @if ($item->ttd_path)
                            <img src="{{ public_path('storage/' . $item->ttd_path) }}" alt="TTD" style="height: 30px;">
                        @else
                            <span class="text-danger">Belum ada</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer untuk tanda tangan -->
    <table class="footer">
        <tr>
            <td width="70%"></td>
            <td>
                {{ str($nama_pelabuhan) }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                <strong>PT Pertamina Trans Kontinental</strong><br><br><br><br>
                <div style="text-align:center;">
                    @if($ttd_pegawai)
                        <img src="{{ public_path('storage/' . $ttd_pegawai) }}" 
                            alt="TTD" 
                            style="width:300px; margin-top:-50px; margin-bottom:-10px; display:block; margin-left:auto; margin-right:auto;">
                    @endif
                    <u style="display:block; margin-top:-25px;">{{ $nama_pegawai }}</u>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

