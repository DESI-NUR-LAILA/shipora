<table style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">
    <thead>
        <!-- Judul -->
        <tr>
            <th colspan="4" style="font-size:14px; font-weight:bold; text-align:center; border:none;">
                DATA USER
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align:center; border:none; font-size:12px;">
                Tanggal : {{ $tanggal }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align:center; border:none; font-size:12px;">
                Pukul : {{ $jam }}
            </th>
        </tr>

        <!-- Tambahkan baris kosong -->
        <tr><th colspan="4" style="border:none;">&nbsp;</th></tr>

        <!-- Header Kolom -->
        <tr style="background-color:#d9d9d9; font-weight:bold;">
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:30px;">No</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:250px;">Email</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:100px;">Hak Akses</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:200px;">Port</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($user as $item)
            <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">{{ $loop->iteration }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->email }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->hak_akses ? $item->hak_akses->nama_hak_akses : '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->port ? $item->port->port : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>