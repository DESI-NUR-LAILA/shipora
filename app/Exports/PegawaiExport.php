<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PegawaiExport implements FromView
{
    protected $pegawai;

    public function __construct($pegawai, $nama_pelabuhan)
    {
        $this->pegawai = $pegawai;
        $this->nama_pelabuhan = $nama_pelabuhan;
    }

    public function view(): View
    {
        return view('pegawai/excel', [
            'nama_pelabuhan' => $this->nama_pelabuhan,
            'pegawai' => $this->pegawai,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H-i-s'),
        ]);
    }
}