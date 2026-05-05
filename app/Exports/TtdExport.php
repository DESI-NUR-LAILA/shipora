<?php

namespace App\Exports;

use App\Models\Ttd;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TtdExport implements FromView
{
    public function view(): View
    {
        $data = array(
            'ttd'  => Ttd::with(['hak_akses', 'port'])
                    ->orderBy('nama', 'asc')
                    ->get(),
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H-i-s'),
        );
        return view('ttd/excel', $data);
    }
}