<?php

namespace App\Exports;

use App\Models\Port;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PortExport implements FromView
{
    public function view(): View
    {
        $data = array(
            'port' => Port::orderBy('port', 'asc')->get(),
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H-i-s'),
        );
        return view('port/excel', $data);
    }
}
