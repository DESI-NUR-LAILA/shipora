<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserExport implements FromView
{
    public function view(): View
    {
        $data = array(
            'user'  => User::with(['hak_akses', 'port'])
                    ->orderBy('email', 'asc')
                    ->get(),
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H-i-s'),
        );
        return view('user/excel', $data);
    }
}
