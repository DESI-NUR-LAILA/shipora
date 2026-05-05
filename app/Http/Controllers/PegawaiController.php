<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Port;
use App\Models\User;
use App\Models\Ttd;
use App\Models\HakAkses;
use App\Exports\PegawaiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Pegawai::query()->where('isarsip', 0); // Hanya yang aktif

        // Jika user adalah Sekretaris / HOA / Supervisor
        if ($user->hak_akses && in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            if ($request->filled('port_id') && $request->port_id !== 'all') {
                // Sekretaris memilih port tertentu
                $query->where('port_id', $request->port_id);
            }
            // Jika memilih "Semua Port", tidak pakai where apapun (tampilkan semua data aktif)
            $pegawai = $query->orderBy('id_card', 'asc')->get();

        } else {
            // Jika bukan Sekretaris (misalnya Admin), tampilkan data aktif di portnya
            $pegawai = $query->where('port_id', $user->port_id)
                ->orderBy('id_card', 'asc')
                ->get();
        }

        // Ambil semua admin untuk dropdown filter
        $admins = User::whereHas('hak_akses', function ($q) {
            $q->where('nama_hak_akses', 'Admin');
        })->get();

        $data = [
            'title' => 'Data Pegawai',
            'menuPengajuan' => 'show',
            'menuPegawai' => 'active',
            'pegawai' => $pegawai,
            'admins' => $admins,
        ];

        return view('pegawai/index', $data);
    }

    public function create()
    {
        $user = auth()->user(); // user yang login

        $data = [
            'title' => 'Tambah Data Pegawai',
            'menuPengajuan' => 'show',
            'menuPegawai' => 'active',
            'userPort' => $user->port, // relasi port dari user
        ];

        return view('pegawai/create', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user(); // user yang sedang login

        $request->validate([
            'id_card' => 'required|unique:pegawais,id_card',
            'nama' => 'required',
            'asal' => 'required',
            'signature' => 'required',
        ], [
            'id_card.required' => 'ID Card wajib diisi.',
            'id_card.unique' => 'ID Card sudah terdaftar.',
            'nama.required' => 'Nama pegawai wajib diisi.',
            'asal.required' => 'Asal wajib diisi.',
            'signature.required' => 'Tanda tangan wajib dibuat.',
        ]);

        // Simpan file tanda tangan
        $imageData = $request->input('signature');
        $image = str_replace(['data:image/png;base64,', ' '], ['', '+'], $imageData);
        $fileName = 'ttd_' . time() . '.png';
        $filePath = 'ttd/' . $fileName;
        \Storage::disk('public')->put($filePath, base64_decode($image));

        // Simpan data pegawai
        $pegawai = Pegawai::create([
            'id_card' => $request->id_card,
            'nama' => $request->nama,
            'bagian' => $request->bagian ?? 'PT. Pertamina Trans Kontinental',
            'port_id' => $user->port_id,
            'jenis_pekerjaan' => $request->jenis_pekerjaan ?? 'Keagenan Domestik',
            'asal' => $request->asal,
            'ttd_path' => $filePath,
        ]);

        return redirect()->route('pegawai', [
            'id' => $pegawai->id,   // sesuai nama parameter di route
        ])->with('success', 'Data pegawai berhasil disimpan');
    }

    public function edit($id)
    {
        $ports = Port::all();

        $data = array(
            'title' => 'Edit Data Pegawai',
            'menuPengajuan' => 'show',
            'menuPegawai' => 'active',
            'pegawai' => Pegawai::findOrFail($id),
            'ports' => $ports,
        );

        return view('pegawai/edit',$data);
    }

    public function update(Request $request, $id)
    {
        // Ambil data pegawai berdasarkan ID
        $pegawai = Pegawai::findOrFail($id);

        // Ambil user yang login
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'id_card' => 'required|unique:pegawais,id_card,' . $pegawai->id,
            'nama'    => 'required',
            'asal'    => 'required',
        ], [
            'id_card.required' => 'ID Card wajib diisi.',
            'id_card.unique'   => 'ID Card sudah terdaftar.',
            'nama.required'    => 'Nama pegawai wajib diisi.',
            'asal.required'    => 'Asal pegawai wajib diisi.',
        ]);

        // Update data
        $pegawai->update([
            'id_card' => $request->id_card,
            'nama' => $request->nama,
            'bagian' => $request->bagian ?? 'PT. Pertamina Trans Kontinental',
            'port_id' => $request->port_id,
            'jenis_pekerjaan' => $request->jenis_pekerjaan ?? 'Keagenan Domestik',
            'asal' => $request->asal,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('pegawai')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function detail(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('pegawai/detail', [
            'title' => 'Detail Pegawai',
            'menuPengajuan' => 'show',
            'menupegawai' => 'active',
            'pegawai' => $pegawai,
            'origin' => $request->input('origin', 'pegawai'),
        ]);
    }

    // Tampilkan pegawai yang diarsipkan
    public function arsip()
    {
        $user = auth()->user();

        // Hanya Admin bisa lihat arsip
        if ($user->hak_akses->nama_hak_akses !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pegawai = Pegawai::where('isarsip', true)
                    ->where('port_id', $user->port_id)
                    ->orderBy('nama', 'asc')
                    ->get();

        return view('pegawai.arsip', [
            'title' => 'Data Arsip Pegawai',
            'menuPengajuan' => 'show',
            'menuPegawai' => 'active',
            'pegawai' => $pegawai,
        ]);
    }

    // Arsipkan pegawai
    public function arsipkan($id)
    {
        $user = auth()->user();

        if (!in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'Admin'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update(['isarsip' => true]);

        return redirect()->route('pegawaiArsip')->with('success', 'Pegawai berhasil dipindahkan ke arsip.');
    }

    // Aktifkan kembali pegawai dari arsip
    public function unarsip($id)
    {
        $user = auth()->user();

        if (!in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'Admin'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update(['isarsip' => false]);

        return redirect()->route('pegawai')->with('success', 'Pegawai berhasil diaktifkan kembali.');
    }

    public function excel(Request $request)
    {
        $user = auth()->user();
        $query = Pegawai::where('isarsip', 0);
        $id_port = null;

        if ($user->hak_akses && in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            // Jika sekretaris memilih filter port_id
            if ($request->filled('port_id') && $request->port_id !== 'all') {
                $query->where('port_id', $request->port_id);
                $id_port = $request->port_id; // supaya TTD ikut sesuai port yang difilter
            }
            // Jika 'all', jangan filter apa pun → tampilkan semua port
        } else {
            // Jika bukan sekretaris, hanya tampilkan pegawai dari port user
            $query->where('port_id', $user->port_id);
            $id_port = $user->port_id;
        }

        $pegawai = $query->orderBy('id_card', 'asc')->get();

        // Menentukan nama pelabuhan di header PDF
        if ($request->filled('port_id') && $request->port_id !== 'all') {
            // Pilih port tertentu
            $port = Port::find($request->port_id);
            $nama_pelabuhan = $port->port ?? 'Jatimbalinus';
        } else {
            // Tidak pilih port / pilih 'all' itu dianggap semua port
            if ($user->hak_akses && in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
                $nama_pelabuhan = 'Jatimbalinus';
            } else {
                $port = Port::find($user->port_id);
                $nama_pelabuhan = $port->port ?? 'Jatimbalinus';
            }
        }

        $filename = now()->format('d-m-Y_H-i-s');

        return Excel::download(new PegawaiExport($pegawai, $nama_pelabuhan), 'DataPegawai_'.$filename.'.xlsx');
    }

    public function pdf(Request $request)
    {
        $user = auth()->user();
        // Filter awal: ambil pegawai yang aktif saja
        $query = Pegawai::where('isarsip', 0);
        $id_port = null;

        if ($user->hak_akses && in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            // Jika sekretaris memilih filter port_id
            if ($request->filled('port_id') && $request->port_id !== 'all') {
                $query->where('port_id', $request->port_id);
                $id_port = $request->port_id; // supaya TTD ikut sesuai port yang difilter
            }
            // Jika 'all', jangan filter apa pun dan menampilkan semua port
        } else {
            // Jika bukan sekretaris, hanya tampilkan pegawai dari port user
            $query->where('port_id', $user->port_id);
            $id_port = $user->port_id;
        }

        $pegawai = $query->orderBy('id_card', 'asc')->get();

        // Tentukan nama pelabuhan di header PDF
        if ($request->filled('port_id') && $request->port_id !== 'all') {
            // Pilih port tertentu
            $port = Port::find($request->port_id);
            $nama_pelabuhan = $port->port ?? 'Jatimbalinus';
        } else {
            // Tidak pilih port / pilih 'all' itu dianggap semua port
            if ($user->hak_akses && in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
                $nama_pelabuhan = 'Jatimbalinus';
            } else {
                $port = Port::find($user->port_id);
                $nama_pelabuhan = $port->port ?? 'Jatimbalinus';
            }
        }

        $filename = now()->format('d-m-Y_H-i-s');

        // Menentukan TTD Pegawai
        $nama_pegawai = '-';
        $ttd_pegawai = null;

        // Ambil ID hak akses Admin dan Sekretaris
        $hakAksesAdmin = HakAkses::where('nama_hak_akses', 'Admin')->first();
        $hakAksesSekretaris = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();

        // Jika user filter port tertentu maka Ambil TTD ADMIN port tersebut
        if ($request->filled('port_id') && $request->port_id !== 'all') {
            $ttd = Ttd::where('port_id', $request->port_id)
                    ->where('hak_akses_id', $hakAksesAdmin->id ?? null)
                    ->where('isarsip', false)
                    ->first();

            $nama_pegawai = $ttd?->nama ?? '-';
            $ttd_pegawai = $ttd?->ttd_path ?? null;

        // Jika filter ALL maka Ambil TTD SEKRETARIS JATIMBALINUS (GLOBAL)
        } elseif ($request->port_id == 'all') {
            $ttd = Ttd::where('hak_akses_id', $hakAksesSekretaris->id ?? null)
                    ->where('isarsip', false)
                    ->first();

            $nama_pegawai = $ttd?->nama ?? '-';
            $ttd_pegawai = $ttd?->ttd_path ?? null;

        // Jika user admin biasa maka Ambil TTD ADMIN port user login
        } else {
            $ttd = Ttd::where('port_id', $user->port_id)
                    ->where('hak_akses_id', $hakAksesAdmin->id ?? null)
                    ->where('isarsip', false)
                    ->first();

            $nama_pegawai = $ttd?->nama ?? '-';
            $ttd_pegawai = $ttd?->ttd_path ?? null;
        }    

        $data = [
            'pegawai' => $pegawai,
            'nama_pelabuhan' => $nama_pelabuhan,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H-i-s'),
            'nama_pegawai' => $nama_pegawai,
            'ttd_pegawai' => $ttd_pegawai,
        ];

        $pdf = Pdf::loadView('pegawai/pdf', $data);
        return $pdf->setPaper('a4', 'landscape')
                    ->stream('DataPegawai_'.$filename.'.pdf');
    }
}