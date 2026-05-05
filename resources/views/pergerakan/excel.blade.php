<table style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">
    <thead>
        <tr>
            <th colspan="13" style="font-size:14px; font-weight:bold; text-align:center; border:none;">
                <strong>LAPORAN KEDATANGAN DAN KEBERANGKATAN KAPAL</strong>
            </th>
        </tr>
        <tr>
            <th colspan="13" style="text-align:center; border:none; font-size:12px;">
                DI PELABUHAN {{ strtoupper($nama_pelabuhan) }}
            </th>
        </tr>
        <tr>
            <th colspan="13" style="text-align:center; border:none; font-size:12px;">
                PERIODE BULAN {{ strtoupper($periode) }}
            </th>
        </tr>

        <!-- Tambahkan baris kosong -->
        <tr><th colspan="13" style="border:none;">&nbsp;</th></tr>

        <tr style="background-color:#d9d9d9; font-weight:bold;">
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:30px;">No</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:200px;">SHIP NAME</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:50px;">GRT</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:50px;">DWT</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:100px;">FLAG</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:150px;">PRINCIPAL</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:100px;">ATA</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:150px;">LAST PORT</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:100px;">ATD</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:150px;">NEXT PORT</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:100px;">ACTIVITIES</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:100px;">JETTY</th>
            <th style="border:1px solid #000; text-align:center; font-weight:bold; font-size:12px; padding:6px; width:150px;">CARGO (MT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pergerakan as $item)
            <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">{{ $loop->iteration }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->ship_name }}</td>
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">{{ $item->grt }}</td>
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">{{ $item->dwt }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->flag }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->principal }}</td>
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">
                    {{ $item->ata ? \Carbon\Carbon::parse($item->ata)->format('d/m/Y') : '-' }}
                </td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->last_port ?? '-' }}</td>
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">
                    {{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d/m/Y') : '-' }}
                </td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->next_port ?? '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->activities ?? '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->jetty ?? '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->cargo ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>