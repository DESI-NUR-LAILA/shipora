<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Trend Dokumen</title>
    <link rel="stylesheet" href="{{ public_path('css/laporan.css') }}">
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo-pertamina.png') }}" alt="Logo Pertamina">
        <h2>LAPORAN TREND DOKUMEN KAPAL</h2>
        <h4>PT PERTAMINA TRANS KONTINENTAL</h4>
        <h4>PERIODE TAHUN {{ $tahun }}</h4>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2">Status Kapal</th>
                <th rowspan="2">Nama Kapal</th>
                <th colspan="12">Bulan</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th>
                <th>Mei</th><th>Jun</th><th>Jul</th><th>Agu</th>
                <th>Sep</th><th>Okt</th><th>Nov</th><th>Des</th>
            </tr>
        </thead>
        <tbody>

        @php
            $grandTotal = array_fill(1, 12, 0);
        @endphp

        @foreach ($data as $status => $kapals)
            @php $first = true; @endphp

            @foreach ($kapals as $kapal => $bulan)
                <tr>
                    {{-- STATUS (ROWSPAN BIAR RAPI) --}}
                    @if ($first)
                        <td class="left" rowspan="{{ count($kapals) }}">
                            {{ strtoupper($status) }}
                        </td>
                        @php $first = false; @endphp
                    @endif

                    {{-- NAMA KAPAL --}}
                    <td class="left">{{ $kapal }}</td>

                    @php $rowTotal = 0; @endphp

                    {{-- BULAN --}}
                    @for ($i = 1; $i <= 12; $i++)
                        <td>{{ $bulan[$i] }}</td>
                        @php
                            $rowTotal += $bulan[$i];
                            $grandTotal[$i] += $bulan[$i];
                        @endphp
                    @endfor

                    {{-- TOTAL PER BARIS --}}
                    <td><b>{{ $rowTotal }}</b></td>
                </tr>
            @endforeach
        @endforeach

        {{-- TOTAL KESELURUHAN --}}
        <tr style="background:#eee; font-weight:bold;">
            <td colspan="2">TOTAL</td>

            @php $sumTotal = 0; @endphp

            @for ($i = 1; $i <= 12; $i++)
                <td>{{ $grandTotal[$i] }}</td>
                @php $sumTotal += $grandTotal[$i]; @endphp
            @endfor

            <td>{{ $sumTotal }}</td>
        </tr>

        </tbody>
    </table>

</body>
</html>