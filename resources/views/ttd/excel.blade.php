<table style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">
    <thead>
        <!-- Judul -->
        <tr>
            <th colspan="5" style="font-size:14px; font-weight:bold; text-align:center; border:none;">
                DATA TANDA TANGAN
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align:center; border:none; font-size:12px;">
                Tanggal : {{ $tanggal }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align:center; border:none; font-size:12px;">
                Pukul : {{ $jam }}
            </th>
        </tr>

        <!-- Tambahkan baris kosong -->
        <tr><th colspan="5" style="border:none;">&nbsp;</th></tr>

        <!-- Header Kolom -->
        <tr style="background-color:#d9d9d9; font-weight:bold;">
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:30px;">No</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:150px;">Nama</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:100px;">Hak Akses</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:100px;">Lokasi Port</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:100px;">Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ttd as $item)
        <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
            <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px; vertical-align: middle;">{{ $loop->iteration }}</td>
            <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->nama }}</td>
            <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->hak_akses->nama_hak_akses }}</td>
            <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->port->port }}</td>
            <td style="border:1px solid #000; padding:5px; font-size:12px; text-align:center; vertical-align: middle; width:100px;">
                @if ($item->ttd_path)
                    <img src="{{ public_path('storage/' . $item->ttd_path) }}"
                        alt="TTD"
                        height="30">
                @else
                    <span class="text-danger">Belum ada</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>