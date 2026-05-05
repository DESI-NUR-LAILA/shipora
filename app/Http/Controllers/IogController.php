<?php

namespace App\Http\Controllers;

use App\Models\Iog;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class IogController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Pegawai::query();

        $iog = Iog::whereHas('pegawai', function ($query) use ($user) {
                $query->where('port_id', $user->port_id);
            })
            ->latest()
            ->get();

        $data = [
            'title' => 'Ijin Olah Gerak',
            'menuPengajuan' => 'show',
            'menuIog' => 'active',
            'iog' => Iog::latest()->get(),
            'iog' => $iog,
        ];

        return view('iog/index', $data);
    }

    public function create()
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Ambil pegawai yang port_id-nya sama dengan port user yang login
        $pegawais = Pegawai::where('port_id', $user->port_id)->get();

        $data = [
            'title'        => 'Tambah Ijin Olah Gerak',
            'menuPengajuan'=> 'show',
            'menuIog'      => 'active',
            'pegawais'     => $pegawais
        ];

        return view('iog/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id'       => 'required|exists:pegawais,id',
            'nomor_surat'      => 'required|regex:/^[0-9]+$/',
            'lampiran'         => 'nullable|integer|min:0',
            'nama_kapal'       => 'required|string|max:255',
            'master'           => 'required|string|max:255',
            'bendera'          => 'nullable|string|max:255', // default Indonesia
            'grt'              => 'required|numeric|min:0',
            'pemilik'          => 'nullable|string|max:255', // default PT. Pertamina Trans Kontinental
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'pegawai_id.required'      => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'        => 'Pegawai yang dipilih tidak ditemukan.',
            'nomor_surat.required'     => 'Nomor surat wajib diisi.',
            'nomor_surat.regex'        => 'Nomor surat hanya boleh berisi angka.',
            'lampiran.min'             => 'Lampiran tidak boleh bernilai negatif.',
            'nama_kapal.required'      => 'Nama kapal wajib diisi.',
            'master.required'          => 'Nama master wajib diisi.',
            'grt.required'             => 'GRT wajib diisi.',
            'grt.numeric'              => 'GRT harus berupa angka.',
            'grt.min'                  => 'GRT tidak boleh bernilai negatif.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'       => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'     => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        // Ambil input nomor urut (misal: 097)
        $urut = $request->nomor_surat;

        // Tambahkan format otomatis
        $bulanRomawi = $this->toRoman(now()->month);
        $fullNomor = "$urut/PTK-BWI/$bulanRomawi/" . now()->year;

        // ✅ Cek duplikasi nomor format lengkap
        if (Iog::where('nomor_surat', $fullNomor)->exists()) {
            return back()
                ->with('error', 'Nomor surat sudah digunakan, silakan pakai nomor lain.')
                ->withInput();
        }

        Iog::create([
            'pegawai_id'      => $request->pegawai_id,
            'nomor_surat'     => $fullNomor,
            'lampiran'        => $request->lampiran,
            'nama_kapal'      => $request->nama_kapal,
            'master'          => $request->master,
            'bendera'         => $request->bendera ?? 'Indonesia',
            'grt'             => $request->grt,
            'pemilik'         => $request->pemilik ?? 'PT. Pertamina Trans Kontinental',
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('iog')->with('success', 'Ijin Olah Gerak berhasil ditambahkan.');
    }

    private function toRoman($month)
    {
        $map = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];

        return $map[$month];
    }

    public function edit($id)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Ambil pegawai yang port_id-nya sama dengan port user yang login
        $pegawais = Pegawai::where('port_id', $user->port_id)->get();

        $data = array(
            'title' => 'Edit Ijin Olah Gerak',
            'menuPengajuan' => 'show',
            'menuIog' => 'active',
            'iog' => Iog::findOrFail($id),
            'pegawais' => $pegawais
        );

        return view('iog/edit',$data);
    }

    public function update(Request $request, $id)
    {
        // Ambil data IOG berdasarkan ID
        $iog = Iog::findOrFail($id);

        // Validasi input
        $request->validate([
            'pegawai_id'       => 'required|exists:pegawais,id',
            'lampiran'         => 'nullable|integer|min:0',
            'nama_kapal'       => 'required|string|max:255',
            'master'           => 'required|string|max:255',
            'bendera'          => 'nullable|string|max:255',
            'grt'              => 'required|numeric|min:0',
            'pemilik'          => 'nullable|string|max:255',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'pegawai_id.required'      => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'        => 'Pegawai yang dipilih tidak ditemukan.',
            'lampiran.min'             => 'Lampiran tidak boleh bernilai negatif.',
            'nama_kapal.required'      => 'Nama kapal wajib diisi.',
            'master.required'          => 'Nama master wajib diisi.',
            'grt.required' => 'GRT wajib diisi.',
            'grt.numeric'  => 'GRT harus berupa angka.',
            'grt.min'      => 'GRT tidak boleh bernilai negatif.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'       => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'     => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        // Simpan nomor surat lama agar tidak bisa diubah
        $nomorSuratTetap = $iog->nomor_surat;

        // Update data
        $iog->update([
            'pegawai_id'      => $request->pegawai_id,
            'nomor_surat'     => $nomorSuratTetap,
            'lampiran'        => $request->lampiran,
            'nama_kapal'      => $request->nama_kapal,
            'master'          => $request->master,
            'bendera'         => $request->bendera ?? 'Indonesia',
            'grt'             => $request->grt,
            'pemilik'         => $request->pemilik ?? 'PT. Pertamina Trans Kontinental',
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('iog')->with('success', 'Ijin Olah Gerak berhasil diperbarui.');
    }

    public function detail($id)
    {
        $iog = Iog::with('pegawai')->findOrFail($id);

        return view('iog/detail', [
            'title' => 'Detail Ijin Olah Gerak',
            'menuPengajuan' => 'show',
            'menuIog' => 'active',
            'iog' => $iog
        ]);
    }

    public function pdf($id)
    {
        $iog = Iog::with('pegawai.port')->findOrFail($id);

        $pdf = Pdf::loadView('iog/pdf', compact('iog'));
        return $pdf->stream('IjinOlahGerak.pdf');
    }
}