<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}"
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN DATA USER</h2>
        <h4>PERTAMINA TRANS KONTINENTAL</h4>
        <h4>PERIODE BULAN {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y')) }}</h4>
    </div>

    <!-- Tabel Data User -->
     <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Email</th>
                <th>Hak Akses</th>
                <th>Port</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($user as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->hak_akses ? $item->hak_akses->nama_hak_akses : '-' }}</td>
                    <td>{{ $item->port ? $item->port->port : '-' }}</td>
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
