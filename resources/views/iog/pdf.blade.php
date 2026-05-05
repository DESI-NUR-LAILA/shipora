<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 1.5cm 2.5cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .logo {
            text-align: right;
            margin-bottom: 20px;
            line-height: 1;
        }
        .logo img {
            width: 320px;
            height: auto;
        }
        .date {
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .letter-info {
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .letter-info p {
            margin: 0;
        }
        .recipient {
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .recipient p {
            margin: 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .content > p {
            margin: 8px 0;
        }
        .data-table {
            margin: 10px 0 10px 15px;
            line-height: 1;
        }
        .data-table tr td {
            padding: 2px 0;
            vertical-align: top;
        }
        .data-table tr td:first-child {
            width: 140px;
        }
        .data-table tr td:nth-child(2) {
            width: 15px;
        }
        
        .signature {
            margin-top: 50px;
        }

        .signature-title {
            width: 240px;         /* biar sejajar sama ttd & nama */
            position: relative;
            text-align: center;
        }

        .signature-space {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120px;
            margin-top: 10px;
        }

        .signature-img {
            width: 240px;
            position: relative;
            top: -10px;
            text-align: center;
        }

        .signature-nama {
            width: 240px;
            position: relative;
            top: -50px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Logo Perusahaan --}}
    <div class="logo">
        <img src="{{ public_path('logo.png') }}">
    </div>

    {{-- Tanggal Surat --}}
    <div class="date">
        {{ $iog->pegawai->port->port }}, {{ \Carbon\Carbon::parse($iog->created_at)->translatedFormat('d F Y') }}
    </div>

    {{-- Informasi Surat --}}
    <div class="letter-info">
        <p>Nomor : {{ $iog->nomor_surat }}</p>
        <p>Lamp. : {{ !empty($iog->lampiran) && $iog->lampiran > 0 ? $iog->lampiran : '-' }}</p>
        <p>Hal.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>Permohonan Ijin Olah Gerak</b></p>
    </div>

    {{-- Penerima Surat --}}
    <div class="recipient">
        <p>Kepada Yth,</p>
        <p><b>Kepala Kesyahbandaran & Otoritas Pelabuhan Klas III</b></p>
        <p><b>Pel. Tg. Wangi</b></p>
        <p>Di –</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banyuwangi</p>
    </div>

    {{-- Isi Surat --}}
    <div class="content">
        <p>Dengan hormat,</p>
        
        <p>Bersama ini kami mohon untuk di berikan Ijin Olah Gerak  kapal keagenan kami di Pel. Tg. Wangi dengan data-data sebagai berikut :</p>

        <table class="data-table">
            <tr>
                <td>- Nama Kapal</td>
                <td>:</td>
                <td>{{ $iog->nama_kapal }}</td>
            </tr>
            <tr>
                <td>- Master</td>
                <td>:</td>
                <td>{{ $iog->master }}</td>
            </tr>
            <tr>
                <td>- Bendera</td>
                <td>:</td>
                <td>{{ $iog->bendera }}</td>
            </tr>
            <tr>
                <td>- GRT</td>
                <td>:</td>
                <td>{{ $iog->grt }} GT </td>
            </tr>
            <tr>
                <td>- Pemilik</td>
                <td>:</td>
                <td>{{ $iog->pemilik }}</td>
            </tr>
            <tr>
                <td>- Keterangan</td>
                <td>:</td>
                <td>Periode <b>{{ \Carbon\Carbon::parse($iog->tanggal_mulai)->translatedFormat('d F Y') }}</b> s/d <b>{{ \Carbon\Carbon::parse($iog->tanggal_selesai)->translatedFormat('d F Y') }}</b></td>
            </tr>
        </table>

        <p>Demikian permohonan ini kami sampaikan, atas perhatian serta kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <p class="signature-title"><b>PT. Pertamina Trans Kontinental</b></p>
        <div class="signature-space">
            @if($iog->pegawai->ttd_path)
                <img src="{{ public_path('storage/' . $iog->pegawai->ttd_path) }}" 
                    alt="TTD Pegawai" 
                    class="signature-img">
            @else
                <div class="signature-space"></div>
            @endif
        </div>
        <p class="signature-nama"><b><u>{{ $iog->pegawai->nama }}</u></b></p>
    </div>

</body>
</html>