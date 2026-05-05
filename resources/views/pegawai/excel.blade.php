<table style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">
    <thead>
        <tr>
            <th colspan="7" style="font-size:14px; font-weight:bold; text-align:center; border:none;">
                Laporan Data Pegawai Pelabuhan {{ Str::title($nama_pelabuhan) }}
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align:center; border:none; font-size:12px;">
                Tanggal : {{ $tanggal }}
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align:center; border:none; font-size:12px;">
                Pukul : {{ $jam }}
            </th>
        </tr>

        <!-- Tambahkan baris kosong -->
        <tr><th colspan="7" style="border:none;">&nbsp;</th></tr>

        <tr style="background-color:#d9d9d9; font-weight:bold;">
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:30px;">No</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:75px;">ID Card</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:150px;">Nama</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:150px;">Asal</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:150px;">Port</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:150px;">Jenis Pekerjaan</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:250px;">Bagian</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; padding:5px; font-size:12px; width:100px;">Tanda Tangan</th>
        </tr>
    </thead>

    <tbody>  
        @foreach ($pegawai as $item)
            <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->id_card }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->nama }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->asal }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->port->port }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->jenis_pekerjaan }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px; vertical-align: middle;">{{ $item->bagian }}</td>

                <td style="border:1px solid #000; text-align:center; padding:5px; vertical-align: middle; width:100px;">
                    @if ($item->ttd_path)
                        <img src="{{ public_path('storage/' . $item->ttd_path) }}" alt="TTD" height="30">
                    @else
                        <span>Belum ada</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>