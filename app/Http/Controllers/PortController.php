<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\User;
use App\Models\Ttd;
use App\Models\HakAkses;
use App\Exports\PortExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PortController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Lokasi Port',
            'menuPort' => 'active',
            'port' => Port::orderBy('port', 'asc')->get(),
        ];

        return view('port/index', $data);
    }

    public function create()
    {
        $data = array(
            'title' => 'Tambah Data Lokasi Port',
            'menuPort' => 'active',
        );

        return view('port/create',$data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'port' => 'required|string|max:255|unique:ports,port',
        ], [
            'port.required' => 'Nama lokasi port wajib diisi.',
            'port.unique'   => 'Nama lokasi port sudah terdaftar.',
            'port.max'      => 'Nama lokasi port maksimal 255 karakter.',
        ]);

        // Simpan data ke database
        Port::create([
            'port' => $request->port,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('port')->with('success', 'Data lokasi port berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = array(
            'title' => 'Edit Data Lokasi Port',
            'menuPort' => 'active',
            'port' => Port::findOrFail($id),
        );
        
        return view('port/edit',$data);
    }

    public function update(Request $request, $id)
    {
        // Ambil data lokasi port berdasarkan ID
        $port = Port::findOrFail($id);

        // Validasi input
        $request->validate([
            'port' => 'required|string|max:255|unique:ports,port,' . $port->id,
        ], [
            'port.required' => 'Nama lokasi port wajib diisi.',
            'port.unique'   => 'Nama lokasi port sudah terdaftar.',
            'port.max'      => 'Nama lokasi port maksimal 255 karakter.',
        ]);

        // Update data
        $port->update([
            'port' => $request->port,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('port')->with('success', 'Data lokasi port berhasil diperbarui.');
    }

    public function excel()
    {
        $filename = now()->format('d-m-Y_H-i-s');
        return Excel::download(new PortExport, 'LokasiPort_'.$filename.'.xlsx');
    }

    public function pdf()
    {
        $filename = now()->format('d-m-Y_H-i-s');

        // Ambil id hak akses Sekretaris
        $sekretarisHakAkses = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();

        // Ambil data TTD sesuai hak akses Sekretaris (yang masih aktif / isarsip = 0)
        $sekretarisTTD = Ttd::where('hak_akses_id', $sekretarisHakAkses->id ?? null)
                    ->where('isarsip', false)
                    ->first();

        $data = [
            'port' => \App\Models\Port::get(),
            'tanggal' => now()->translatedFormat('d F Y'),
            'nama_sekretaris' => $sekretarisTTD->nama ?? '-',
            'ttd_sekretaris' => $sekretarisTTD->ttd_path ?? null,
        ];

        $pdf = Pdf::loadView('port.pdf', $data);
        return $pdf->setPaper('a4', 'portrait')->stream('LokasiPort_' . $filename . '.pdf');
    }
}