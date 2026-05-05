<table style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">
    <thead>
        <tr>
            <th colspan="8" style="font-size:16px; font-weight:bold; padding-top:6px; text-align:center; border:none;">
                <strong>LAPORAN SOD</strong>
            </th>
        </tr>
        <tr>
            <th colspan="8" style="padding:2px; text-align:center; border:none; font-size:12px;">
                DI PELABUHAN {{ strtoupper($nama_pelabuhan) }}
            </th>
        </tr>
        <tr>
            <th colspan="8" style="padding-bottom:8px; text-align:center; border:none; font-size:12px;">
                PERIODE BULAN {{ strtoupper($periode) }}
            </th>
        </tr>
        <tr style="background-color:#d9d9d9; font-weight:bold;">
            <th style="border:1px solid #000; text-align:center; padding:6px; width:30px;">No</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:150px;">SHIP NAME</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:150px;">STATUS KAPAL</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:100px;">ATA</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:100px;">ATD</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:150px;">LAST PORT</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:150px;">NEXT PORT</th>
            <th style="border:1px solid #000; text-align:center; padding:6px; width:150px;">STATUS LAPORAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan as $item)
         <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                <td style="border:1px solid #000; text-align:center; padding:5px; font-size:12px;">{{ $loop->iteration }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->pergerakan->ship_name }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{  $item->pergerakan->status }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">
                    {{ $item->pergerakan->ata ? \Carbon\Carbon::parse($item->ata)->format('d/m/Y') : '-' }}
                </td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">
                    {{ $item->pergerakan->atd ? \Carbon\Carbon::parse($item->atd)->format('d/m/Y') : '-' }}
                </td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->pergerakan->last_port ?? '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->pergerakan->next_port ?? '-' }}</td>
                <td style="border:1px solid #000; padding:5px; font-size:12px;">{{ $item->status ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>