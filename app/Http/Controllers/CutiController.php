<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Ttd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Cuti::query()->with('pegawai.port'); // langsung relasi pegawai + port

        // Filter berdasarkan port_id (dari dropdown)
        if ($request->has('port_id') && $request->port_id != '') {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('port_id', $request->port_id);
            });
        }

        // Filter berdasarkan hak_akses
        if ($user->hak_akses) {
            $hak_akses = $user->hak_akses->nama_hak_akses;

            switch ($hak_akses) {
                case 'Admin':
                    $query->whereHas('pegawai', function($q) use ($user) {
                        // Ambil cuti pegawai yang port_id sama dengan Admin
                        $q->where('port_id', $user->port_id);
                    });
                    break;

                case 'Sekretaris':
                case 'HOA':
                    // Sekretaris, HOA, Supervisor hanya lihat cuti yang sudah diketahui atau disetujui
                    $query->whereIn('status', ['disetujui']);
                    break;
                case 'Supervisor':
                    // Supervisor lihat cuti yang statusnya pending ATAU mengetahui
                    $query->whereIn('status', ['pending', 'diketahui', 'disetujui']);
                    break;

                case 'PIC':
                    // PIC hanya lihat cuti yang sudah diketahui oleh Supervisor
                    $query->whereIn('status', ['diketahui', 'disetujui', 'ditolak']);
                    break;

                default:
                    // Default: lihat cuti sendiri
                    $query->where('pegawai_id', $user->id);
            }
        }

        $cuti = $query->latest()->get();

        // Ambil semua admin untuk filter dropdown Sekretaris/HOA
        $admins = User::whereHas('hak_akses', function ($q) {
            $q->where('nama_hak_akses', 'Admin');
        })->get();

        return view('cuti/index', [
            'title' => 'Surat Cuti',
            'menuPengajuan' => 'show',
            'menuCuti' => 'active',
            'cuti' => $cuti,
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        $pegawais = Pegawai::where('port_id', Auth::user()->port_id)->get();

        $data = array(
            'title' => 'Tambah Surat Cuti',
            'menuPengajuan' => 'show',
            'menuCuti' => 'active',
            'pegawais' => $pegawais
        );

        return view('cuti/create',$data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id'       => 'required|exists:pegawais,id',
            'tujuan'           => 'required|string|max:255',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'       => 'required|string|max:255',
            'berkendaraan'     => 'nullable|in:Pribadi,Umum',
            'hari_libur'       => 'required|integer|min:0',
        ],[
            'pegawai_id.required'      => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'        => 'Pegawai tidak ditemukan dalam data.',
            'tujuan.required'          => 'Tujuan perjalanan wajib diisi.',
            'tujuan.max'               => 'Tujuan perjalanan maksimal 255 karakter.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'       => 'Format tanggal mulai tidak valid.',         
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'     => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',       
            'keterangan.required'      => 'Keterangan perjalanan wajib diisi.',
            'keterangan.max'           => 'Keterangan maksimal 255 karakter.',        
            'berkendaraan.in'          => 'Jenis kendaraan harus salah satu dari: Pribadi, Umum.',        
            'hari_libur.required'      => 'Jumlah hari libur wajib diisi.',
            'hari_libur.integer'       => 'Jumlah hari libur harus berupa angka.',
            'hari_libur.min'           => 'Jumlah hari libur minimal 0.',
        ]);

        $pegawaiId = $request->pegawai_id;

        // Cek TTD Pegawai
        $pegawai = Pegawai::findOrFail($pegawaiId);
        if (empty($pegawai->ttd_path)) {
            return redirect()
                ->route('ttdCreate', [$pegawaiId, 'pegawai'])
                ->with('warning', 'Pegawai ini belum memiliki tanda tangan. Silakan unggah tanda tangan terlebih dahulu.');
        }

        // Cek apakah masih ada cuti aktif
        $cutiBelumSelesai = Cuti::where('pegawai_id', $pegawaiId)
            ->whereIn('status', ['pending', 'diketahui'])
            ->exists();

        if ($cutiBelumSelesai) {
            return back()->with('error', 'Pengajuan cuti sebelumnya belum selesai diproses.');
        }

        // Hitung lama hari
        $tanggalMulai   = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $totalHari      = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $hariLibur = (int) $request->hari_libur;

        // 🔥 VALIDASI LOGIKA HARI LIBUR (4 kerja : 2 libur)
        $siklus = floor($totalHari / 6);
        $sisa   = $totalHari % 6;

        $maxHariLibur = ($siklus * 2) + min(2, $sisa);

        if ($hariLibur > $maxHariLibur) {
            return back()->withErrors([
                'hari_libur' => "Jumlah hari libur tidak logis. Maksimal hanya $maxHariLibur hari untuk rentang tanggal tersebut."
            ])->withInput();
        }

        // Jika input melebihi total hari (safety tambahan)
        if ($hariLibur > $totalHari) {
            $hariLibur = $totalHari;
        }

        $lamaHari = $totalHari - $hariLibur;

        // ⛔ Tidak boleh cuti 0 hari kerja
        if ($lamaHari < 1) {
            return back()->with('error', 'Tidak dapat mengajukan cuti. Tidak ada hari kerja dalam rentang tanggal tersebut.')->withInput();
        }

        // ⛔ Maksimal cuti diambil sekaligus = 6 hari
        if ($lamaHari > 6) {
            return back()->with('error', 'Cuti hanya boleh diambil maksimal 6 hari dalam satu kali pengajuan.');
        }

        // Hitung sisa hak cuti
        $tahun = date('Y', strtotime($request->tanggal_mulai));

        $cutiTerpakai = Cuti::where('pegawai_id', $pegawaiId)
            ->whereYear('tanggal_mulai', $tahun)
            ->where('status', 'disetujui')
            ->sum('lama_hari');

        $hakCutiTahunan = 12;
        $sisaHakCuti = $hakCutiTahunan - $cutiTerpakai;

        if ($lamaHari > $sisaHakCuti) {
            return back()->withErrors([
                'tanggal_selesai' => "Sisa hak cuti anda hanya $sisaHakCuti hari."
            ])->withInput();
        }

        // Cek overlap
        $overlap = Cuti::where('pegawai_id', $pegawaiId)
            ->whereIn('status', ['pending', 'diketahui', 'disetujui'])
            ->where(function($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function($q) use ($tanggalMulai, $tanggalSelesai) {
                        $q->where('tanggal_mulai', '<=', $tanggalMulai)
                        ->where('tanggal_selesai', '>=', $tanggalSelesai);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Tidak dapat mengajukan cuti karena terdapat pengajuan cuti lain pada rentang tanggal tersebut.');
        }

        // Simpan cuti
        Cuti::create([
            'pegawai_id'      => $pegawaiId,
            'tujuan'          => $request->tujuan,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'hari_libur'      => $hariLibur,
            'lama_hari'       => $lamaHari,
            'sisa_hak_cuti'   => $sisaHakCuti,
            'keterangan'      => $request->keterangan,
            'berkendaraan'    => $request->berkendaraan,
            'status'          => 'pending',
        ]);

        return redirect()->route('cuti')->with('success', 'Pengajuan cuti berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pegawais = Pegawai::where('port_id', Auth::user()->port_id)->get();

        $data = array(
            'title' => 'Edit Data Pegawai',
            'menuPengajuan' => 'show',
            'menuCuti' => 'active',
            'cuti' => Cuti::findOrFail($id),
            'pegawais' => $pegawais
        );
        
        return view('cuti/edit',$data);
    }

    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        $request->validate([
            'pegawai_id'       => 'required|exists:pegawais,id',
            'tujuan'           => 'required|string|max:255',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'       => 'required|string|max:255',
            'berkendaraan'     => 'nullable|in:Pribadi,Umum',
            'hari_libur'       => 'required|integer|min:0',
        ],[
            'pegawai_id.required'      => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'        => 'Pegawai tidak ditemukan dalam data.',
            'tujuan.required'          => 'Tujuan perjalanan wajib diisi.',
            'tujuan.max'               => 'Tujuan perjalanan maksimal 255 karakter.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'       => 'Format tanggal mulai tidak valid.',         
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'     => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',       
            'keterangan.required'      => 'Keterangan perjalanan wajib diisi.',
            'keterangan.max'           => 'Keterangan maksimal 255 karakter.',        
            'berkendaraan.in'          => 'Jenis kendaraan harus salah satu dari: Pribadi, Umum.',        
            'hari_libur.required'      => 'Jumlah hari libur wajib diisi.',
            'hari_libur.integer'       => 'Jumlah hari libur harus berupa angka.',
            'hari_libur.min'           => 'Jumlah hari libur minimal 0.',
        ]);

        $pegawaiId = $request->pegawai_id;

        $tanggalMulai   = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $totalHari      = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $hariLibur = (int) $request->hari_libur;

        // 🔥 VALIDASI LOGIKA HARI LIBUR (4 kerja : 2 libur)
        $siklus = floor($totalHari / 6);
        $sisa   = $totalHari % 6;

        $maxHariLibur = ($siklus * 2) + min(2, $sisa);

        if ($hariLibur > $maxHariLibur) {
            return back()->withErrors([
                'hari_libur' => "Jumlah hari libur tidak logis. Maksimal hanya $maxHariLibur hari untuk rentang tanggal tersebut."
            ])->withInput();
        }

        // Safety tambahan
        if ($hariLibur > $totalHari) {
            $hariLibur = $totalHari;
        }

        $lamaHari = $totalHari - $hariLibur;

        // ⛔ Tidak boleh 0 hari kerja
        if ($lamaHari < 1) {
            return back()->with('error', 'Tidak dapat menyimpan cuti. Tidak ada hari kerja dalam rentang tanggal tersebut.')->withInput();
        }

        // ⛔ Maksimal 6 hari kerja
        if ($lamaHari > 6) {
            return back()->with('error', 'Cuti hanya boleh diambil maksimal 6 hari dalam satu kali pengajuan.');
        }

        $tahun = date('Y', strtotime($request->tanggal_mulai));

        $cutiTerpakai = Cuti::where('pegawai_id', $pegawaiId)
            ->whereYear('tanggal_mulai', $tahun)
            ->where('status', 'disetujui')
            ->where('id', '!=', $cuti->id)
            ->sum('lama_hari');

        $hakCutiTahunan = 12;
        $sisaHakCuti = $hakCutiTahunan - $cutiTerpakai;

        if ($lamaHari > $sisaHakCuti) {
            return back()->withErrors([
                'tanggal_selesai' => "Sisa hak cuti anda hanya $sisaHakCuti hari."
            ])->withInput();
        }

        // Reset approval jika sebelumnya sudah diketahui
        if ($cuti->status === 'diketahui') {
            $cuti->status = 'pending';
            $cuti->mengetahui_id = null;
            $cuti->menyetujui_id = null;
        }

        // Cek overlap
        $overlap = Cuti::where('pegawai_id', $pegawaiId)
            ->where('id', '!=', $cuti->id)
            ->whereIn('status', ['pending', 'diketahui', 'disetujui'])
            ->where(function($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function($q) use ($tanggalMulai, $tanggalSelesai) {
                        $q->where('tanggal_mulai', '<=', $tanggalMulai)
                        ->where('tanggal_selesai', '>=', $tanggalSelesai);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Tidak dapat menyimpan perubahan karena tanggal cuti bertumpuk dengan pengajuan lain.');
        }

        $cuti->update([
            'pegawai_id'      => $pegawaiId,
            'tujuan'          => $request->tujuan,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'hari_libur'      => $hariLibur,
            'lama_hari'       => $lamaHari,
            'sisa_hak_cuti'   => $sisaHakCuti,
            'keterangan'      => $request->keterangan,
            'berkendaraan'    => $request->berkendaraan,
            'status'          => $cuti->status,
        ]);

        return redirect()->route('cuti')->with('success', 'Surat Cuti berhasil diperbarui.');
    }

    public function detail($id)
    {
        $cuti = Cuti::with('pegawai')->findOrFail($id);

        return view('cuti/detail', [
            'title' => 'Detail Surat Cuti',
            'menuPengajuan' => 'show',
            'menuCuti' => 'active',
            'cuti' => $cuti
        ]);
    }

    public function mengetahui($id)
    {
        // Ambil data cuti, pastikan ada atau tampilkan 404
        $cuti = Cuti::findOrFail($id);

        // Pastikan user yang login adalah Supervisor
        $user = auth()->user();
        if ($user->hak_akses->nama_hak_akses !== 'Supervisor') {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        // Cek apakah cuti sudah disetujui
        if ($cuti->status === 'disetujui') {
            return redirect()->back()->with('warning', 'Surat cuti sudah disetujui, tidak dapat diubah.');
        }

        // Ambil TTD Supervisor berdasarkan hak_akses_id saja
        $ttd = Ttd::where('hak_akses_id', $user->hak_akses_id)->first();

        if (!$ttd) {
            return redirect()->back()->with('error', 'Tanda tangan Supervisor belum diupload di menu TTD.');
        }

        // Update status cuti menjadi "diketahui" dan simpan TTD Supervisor
        $cuti->update([
            'status' => 'diketahui',
            'mengetahui_id' => $ttd->id,
        ]);

        return redirect()->back()->with('success', 'Surat cuti telah diketahui.');
    }

    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Pastikan user yang login adalah Supervisor
        $user = auth()->user();
        if ($user->hak_akses->nama_hak_akses !== 'PIC') {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        // Ambil TTD PIC
        $ttd = Ttd::where('hak_akses_id', $user->hak_akses_id)->first();
        if (!$ttd) {
            return redirect()->back()->with('error', 'Tanda tangan PIC belum diupload di menu TTD.');
        }

        // Cek apakah cuti sudah disetujui sebelumnya
        if ($cuti->status === 'disetujui') {
            return redirect()->back()->with('warning', 'Surat cuti sudah disetujui, tidak dapat diubah.');
        }

        $tahun = date('Y', strtotime($cuti->tanggal_mulai));

        // Hitung cuti yang sudah disetujui sebelumnya
        $cutiTerpakai = Cuti::where('pegawai_id', $cuti->pegawai_id)
            ->whereYear('tanggal_mulai', $tahun)
            ->where('status', 'disetujui')
            ->where('id', '!=', $cuti->id)
            ->sum('lama_hari');

        $hakCutiTahunan = 12;
        $sisaHakCuti = $hakCutiTahunan - $cutiTerpakai;

        // Jika tidak cukup hak cuti → tolak pengajuan
        if ($cuti->lama_hari > $sisaHakCuti) {
            return redirect()->back()->with('error', "Sisa hak cuti hanya $sisaHakCuti hari. Tidak dapat menyetujui cuti.");
        }

        // Set status dan kurangi hak cuti
        $cuti->update([
            'status' => 'disetujui',
            'sisa_hak_cuti' => $sisaHakCuti - $cuti->lama_hari,
            'menyetujui_id' => $ttd->id,
        ]);

        return redirect()->back()->with('success', 'Surat cuti telah disetujui.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $cuti = Cuti::findOrFail($id);
        $cuti->status = 'ditolak';
        $cuti->alasan_penolakan = $request->alasan_penolakan;
        $cuti->save();

        return redirect()->back()->with('success', 'Surat cuti berhasil ditolak.');
    }

    public function pdf($id)
    {
        $cuti = Cuti::with([
            'pegawai', 
            'pegawai.port',
            'mengetahui',
            'menyetujui',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('cuti/pdf', compact('cuti'));
        return $pdf->stream('surat_cuti.pdf');
    }
  
}