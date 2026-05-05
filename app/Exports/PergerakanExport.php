<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class PergerakanExport implements FromView
{
    protected $bulan;
    protected $tahun;

    // Konstruktor menerima data yang sudah difilter
    public function __construct($nama_pelabuhan, $pergerakan, $bulan, $tahun, $periode)
    {
        $this->nama_pelabuhan = $nama_pelabuhan;
        $this->pergerakan = $pergerakan;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->periode = $periode;
    }

    public function view(): View
    {
        return view('pergerakan/excel', [
            'nama_pelabuhan' => $this->nama_pelabuhan,
            'pergerakan' => $this->pergerakan,
            'tanggal'    => Carbon::now()->format('d-m-Y'),
            'jam'        => Carbon::now()->format('H:i:s'),
            'periode'    => $this->periode,
        ]);
    }
}