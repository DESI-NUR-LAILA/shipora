<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class LaporanExport implements FromView
{
    protected $bulan;
    protected $tahun;

    // Konstruktor menerima data yang sudah difilter
    public function __construct($nama_pelabuhan, $laporan, $bulan, $tahun, $periode)
    {
        $this->nama_pelabuhan = $nama_pelabuhan;
        $this->laporan = $laporan;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->periode = $periode;
    }

    public function view(): View
    {
        return view('aporan/excel', [
            'nama_pelabuhan' => $this->nama_pelabuhan,
            'laporan' => $this->laporan,
            'tanggal'    => Carbon::now()->format('d-m-Y'),
            'jam'        => Carbon::now()->format('H:i:s'),
            'periode'    => $this->periode,
        ]);
    }
}