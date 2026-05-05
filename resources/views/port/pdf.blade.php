<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Lokasi Port</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}"
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN DATA LOKASI PORT</h2>
        <h4>PERTAMINA TRANS KONTINENTAL</h4>
        <h4>PERIODE BULAN {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y')) }}</h4>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Lokasi Port</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($port as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->port }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer untuk tanda tangan -->
    <table class="footer">
        <tr>
            <td width="70%"></td>
            <td>
                Jagir, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                <strong>PT Pertamina Trans Kontinental</strong>
                <div style="text-align:center;">
                    @if($ttd_sekretaris)
                        <img src="{{ public_path('storage/' . $ttd_sekretaris) }}" 
                            alt="TTD Sekretaris" 
                            style="width:300px; display:block; margin:5px auto 0 auto;">
                    @endif
                    <u style="display:block; margin-top:-30px;">{{ $nama_sekretaris }}</u>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

