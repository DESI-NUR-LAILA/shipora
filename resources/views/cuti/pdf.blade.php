<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Surat Hak Cuti</title>
        <style>
            body {
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
                color: #000;
                margin: 20px;
                padding: 0;
            }
            
            /* Header Styles */
            .header-wrapper {
                margin-bottom: 15px;
            }
            
            .header-container {
                display: table;
                width: 100%;
                margin-bottom: 10px;
            }
            .logo-cell {
                display: table-cell;
                width: 160px;
                vertical-align: middle;
            }
            .logo-cell img {
                width: 140px;
                height: auto;
            }
            .title-cell {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
                padding: 0 20px;
            }
            .title-cell h2 {
                margin: 15px 0 0;
                font-size: 22px;
                font-weight: bold;
                color: #003399;
                line-height: 1.3;
                white-space: nowrap;
            }
            .title-cell h3 {
                margin: 20px 0 0;
                font-size: 20px;
                font-weight: bold;
                color: #000;
                line-height: 1.3;
            }
            .spacer-cell {
                display: table-cell;
                width: 160px;
            }
            
            .header-line {
                width: 100%;
                height: 1px;
                background-color: #000;
                margin-top: 10px;
            }
            
            /* Content Styles */
            .content {
                margin: 20px 30px;
            }
            
            .section-title {
                margin: 20px 0 10px;
                font-size: 15px;
            }
            
            .info-table {
                width: 100%;
                margin-bottom: 15px;
                border-collapse: collapse;
            }
            .info-table td {
                padding: 4px 0;
                vertical-align: top;
                font-size: 14px;
            }
            .info-table td:first-child {
                width: 220px;
            }
            .info-table td:nth-child(2) {
                width: 15px;
            }
            .fw-bold {
                font-weight: bold;
            }
            
            /* Date and Signature */
            .date-text {
                font-weight: bold;
                text-align: right;
                margin: 30px 30px 20px;
                font-size: 14px;
            }
            
            .signature-wrapper {
                margin: 30px 20px 20px;
            }
            
            .signature-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }
            
            .signature-table td {
                width: 32%;
                padding: 0 5px;
                vertical-align: top;
            }
            
            .signature-box {
                border: 1px solid #000;
                width: 100%;
            }
            
            .signature-header {
                border-bottom: 1px solid #000;
                padding: 12px 8px;
                text-align: center;
                min-height: 50px;
            }
            
            .signature-body {
                padding: 10px 8px;
                text-align: center;
                height: 125px;
                vertical-align: bottom;
            }

            .signature-space {
                height: 100px;           
                display: flex;
                justify-content: center; 
                align-items: center;
            }

            .signature{
                width:300px; 
                height:auto; 
                margin-top:-25px;"
            }
            
            .signature-label {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 5px;
            }
            
            .signature-title {
                font-weight: bold;
                font-size: 10px;
                line-height: 1.3;
                margin-top: 3px;
            }
            
            .signature-name {
                font-weight: bold;
                font-size: 12px;
                text-align: center;
                padding-top: 10px;
            }
            
            /* Print Optimization */
            @media print {
                body {
                    margin: 0;
                    padding: 15px;
                }
            }
        </style>
    </head>
    <body>

        {{-- Header --}}
        <div class="header-wrapper">
            <div class="header-container">
                <div class="logo-cell">
                    <img src="{{ public_path('pertamina.png') }}" alt="Logo Pertamina">
                </div>
                <div class="title-cell">
                    <h2>PT. PERTAMINA MARINE SOLUTIONS</h2>
                    <h3>SURAT HAK CUTI</h3>
                </div>
                <div class="spacer-cell"></div>
            </div>
            <div class="header-line"></div>
        </div>

        {{-- Content --}}
        <div class="content">
            <div class="section-title">Dengan ini</div>
            
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td class="fw-bold">{{ $cuti->pegawai->nama }}</td>
                </tr>
                <tr>
                    <td>No. ID Card</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->id_card }}</td>
                </tr>
                <tr>
                    <td>Bagian / Penempatan</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->bagian }} / {{ $cuti->pegawai->port->port }}</td>
                </tr>
                <tr>
                    <td>Jenis Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->jenis_pekerjaan }}</td>
                </tr>
            </table>

            <div class="section-title">Untuk melaksanakan HAK CUTI</div>
            
            <table class="info-table">
                <tr>
                    <td>Dari / Asal</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->asal }}</td>
                </tr>
                <tr>
                    <td>Tempat / Tujuan</td>
                    <td>:</td>
                    <td>{{ $cuti->tujuan }}</td>
                </tr>
                <tr>
                    <td>Terhitung mulai tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Sampai tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Keperluan / Keterangan</td>
                    <td>:</td>
                    <td>{{ $cuti->keterangan }}</td>
                </tr>
                <tr>
                    <td>Berkendaraan</td>
                    <td>:</td>
                    <td>{{ $cuti->berkendaraan }}</td>
                </tr>
            </table>

            <table class="info-table" style="margin-top: 20px;">
                <tr>
                    <td>Lama Cuti</td>
                    <td>:</td>
                    <td>{{ $cuti->lama_hari }} Hari Kerja, {{ $cuti->hari_libur }} Hari Libur</td>
                </tr>
                <tr>
                    <td>Sisa Hak Cuti</td>
                    <td>:</td>
                    <td>{{ $cuti->sisa_hak_cuti }} Hari Kerja</td>
                </tr>
            </table>
        </div>

        {{-- Date --}}
        <div class="date-text">
            {{ $cuti->pegawai->port->port }}, {{ \Carbon\Carbon::parse($cuti->created_at)->translatedFormat('d F Y') }}
        </div>

        {{-- Signature Boxes --}}
        <div class="signature-wrapper">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-box">
                            <div class="signature-header">
                                <div class="signature-label">MENGETAHUI,</div>
                                <div class="signature-title">Sr. Supervisor Agency Operation Surabaya</div>
                            </div>
                            <div class="signature-body">
                                <div class="signature-space">
                                    @if($cuti->mengetahui && $cuti->mengetahui->ttd_path)
                                        <img src="{{ public_path('storage/' . $cuti->mengetahui->ttd_path) }}" alt="TTD Supervisor" class="signature">
                                    @else
                                        <div class="signature-space"></div>
                                    @endif
                                </div>
                                <div class="signature-name">{{ $cuti->mengetahui->nama ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="signature-box">
                            <div class="signature-header">
                                <div class="signature-label">MENYETUJUI,</div>
                                <div class="signature-title">PT. Pertamina Marine Solutions Cab. Surabaya</div>
                            </div>
                            <div class="signature-body">
                                <div class="signature-space">
                                    @if($cuti->menyetujui && $cuti->menyetujui->ttd_path)
                                        <img src="{{ public_path('storage/' . $cuti->menyetujui->ttd_path) }}" alt="TTD PIC" class="signature">
                                    @else
                                        <div class="signature-space"></div>
                                    @endif
                                </div>
                                <div class="signature-name">{{ $cuti->menyetujui->nama ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="signature-box">
                            <div class="signature-header">
                                <div class="signature-label">PEMOHON CUTI :</div>
                            </div>
                            <div class="signature-body">
                                <div class="signature-space">
                                    @if($cuti->pegawai->ttd_path)
                                        <img src="{{ public_path('storage/' . $cuti->pegawai->ttd_path) }}" alt="TTD Pegawai" class="signature">
                                    @else
                                        <div class="signature-space"></div>
                                    @endif
                                </div>
                                <div class="signature-name">{{ $cuti->pegawai->nama }}</div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </body>
</html>