<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TtdExport;
use App\Models\Ttd;
use App\Models\Port;
use App\Models\HakAkses;
use App\Models\User;

class TtdController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $isArsip = $request->get('arsip', false);

        $ttd = Ttd::with('user.hak_akses', 'user.port')
            ->orderBy('nama', 'asc')
            ->when($isArsip, fn($q) => $q->where('isarsip', true))
            ->when(!$isArsip, fn($q) => $q->where('isarsip', false))
            ->get();

        return view('ttd.index', [
            'title' => $isArsip ? 'Arsip Tanda Tangan' : 'Tanda Tangan',
            'menuPengajuan' => 'show',
            'menuTtd' => 'active',
            'ttd' => $ttd,
            'isArsip' => $isArsip,
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $ports = Port::all();
        $hak_akses = HakAkses::all();

        return view('ttd.create', [
            'title' => 'Buat Tanda Tangan',
            'menuPengajuan' => 'show',
            'menuTtd' => 'active',
            'ports' => $ports,
            'hak_akses' => $hak_akses,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk menyimpan tanda tangan.');
        }

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'hak_akses_id' => 'required|exists:hak_akses,id',
            'port_id' => [
                'required',
                'exists:ports,id',
            ],
            'signature' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'hak_akses_id.required' => 'Hak akses wajib dipilih.',
            'hak_akses_id.exists' => 'Hak akses tidak valid.',
            'port_id.required' => 'Lokasi port wajib dipilih.',
            'port_id.exists' => 'Port yang dipilih tidak valid.',
            'signature.required' => 'Tanda tangan wajib dibuat.',
        ]);

        // Daftar hak akses yang hanya boleh punya 1 tanda tangan aktif
        $singleAccess = ['Sekretaris', 'PIC', 'HOA', 'Supervisor'];

        // Cek apakah hak akses yang dipilih hanya boleh punya 1 tanda tangan
        $selectedHakAkses = HakAkses::find($request->hak_akses_id);

        if (in_array($selectedHakAkses->nama_hak_akses, $singleAccess)) {

            // Cek apakah sudah punya tanda tangan aktif tanpa melihat port
            $exists = Ttd::where('hak_akses_id', $request->hak_akses_id)
                ->where('isarsip', false)
                ->exists();

        } else {
            // Jika bukan hak akses khusus maka cek kombinasi hak_akses dan port
            $exists = Ttd::where('hak_akses_id', $request->hak_akses_id)
                ->where('port_id', $request->port_id)
                ->where('isarsip', false)
                ->exists();
        }

        if ($exists) {
            return back()->with('error', 'Tanda tangan aktif untuk hak akses ini sudah ada. Nonaktifkan dulu sebelum menambah baru.');
        }

        // Simpan file tanda tangan
        $imageData = $request->input('signature');
        $image = str_replace(['data:image/png;base64,', ' '], ['', '+'], $imageData);
        $fileName = 'ttd_' . time() . '.png';
        $filePath = 'ttd/' . $fileName;
        \Storage::disk('public')->put($filePath, base64_decode($image));

        // Simpan ke database
        Ttd::create([
            'nama' => $request->nama,
            'hak_akses_id' => $request->hak_akses_id,
            'port_id' => $request->port_id,
            'ttd_path' => $filePath,
            'isarsip' => false,
        ]);

        return redirect()->route('ttd')->with('success', 'Tanda tangan berhasil disimpan.');
    }

    public function arsip()
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $ttd = Ttd::with('user')
                  ->where('isarsip', true)
                  ->orderBy('nama', 'asc')
                  ->get();

        return view('ttd.arsip', [
            'title' => 'Data Arsip Tanda Tangan',
            'menuPengajuan' => 'show',
            'menuTtd' => 'active',
            'ttd' => $ttd,
        ]);
    }

    public function arsipkan($id)
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $ttd = Ttd::findOrFail($id);
        $ttd->update(['isarsip' => true]);

        return redirect()->route('ttdArsip')->with('success', 'Data berhasil dipindahkan ke arsip.');
    }

    public function unarsip($id)
    {
        $user = auth()->user();

        if ($user->hak_akses->nama_hak_akses !== 'Sekretaris') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $ttd = Ttd::findOrFail($id);

        // Cek apakah sudah ada TTD aktif lain untuk kombinasi hak akses dan port yang sama
        $exists = Ttd::where('hak_akses_id', $ttd->hak_akses_id)
            ->where('port_id', $ttd->port_id)
            ->where('isarsip', false)
            ->exists();

        if ($exists) {
            // Jika ada aktif lain, tampilkan pesan error
            return redirect()->route('ttdArsip')->with('error', 'Sudah ada tanda tangan aktif untuk Hak Akses dan Port ini. Nonaktifkan terlebih dahulu sebelum mengaktifkan kembali.');
        }

        // Aktifkan kembali TTD
        $ttd->update(['isarsip' => false]);

        return redirect()->route('ttd')->with('success', 'Tanda tangan berhasil diaktifkan kembali.');
    }

    public function excel()
    {
        $filename = now()->format('d-m-Y_H-i-s');
        return Excel::download(new TtdExport, 'DataTtd_' . $filename . '.xlsx');
    }

    public function pdf()
    {
        $filename = now()->format('d-m-Y_H-i-s');

        // Ambil semua user beserta relasinya
        $ttds = Ttd::with(['hak_akses', 'port'])->get();

        // Ambil hak akses Sekretaris
        $sekretarisHakAkses = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();

        // Cari TTD aktif untuk Sekretaris (isarsip = 0)
        $sekretarisTTD = Ttd::where('hak_akses_id', $sekretarisHakAkses->id ?? null)
            ->where('isarsip', false)
            ->first();

        $data = [
            'ttd' => $ttds,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
            'nama_sekretaris' => $sekretarisTTD->nama ?? '-', // ambil nama dari tabel ttd
            'ttd_sekretaris' => $sekretarisTTD->ttd_path ?? null, // ambil ttd dari ttd_path
        ];

        $pdf = Pdf::loadView('ttd/Pdf', $data);
        return $pdf->setPaper('a4', 'potrait')->stream('DataTtd_' . $filename . '.pdf');
    }

}