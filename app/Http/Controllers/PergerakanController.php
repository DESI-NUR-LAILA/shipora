<?php

namespace App\Http\Controllers;

use App\Models\Pergerakan;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Ttd;
use App\Models\HakAkses;
use App\Exports\PergerakanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PergerakanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user(); 
        $query = Pergerakan::query();

        // Jika user adalah sekretaris atau HOA atau Supervisor
        if (auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            // Jika memilih filter user_id (admin)
            if ($request->has('user_id') && $request->user_id != '' && $request->user_id != 'all') {
                $query->where('user_id', $request->user_id);
            }
            $pergerakan = $query->orderBy('atd', 'asc')->get();
        } else {
            // Jika bukan sekretaris atau HOA atau Supervisor, hanya tampilkan miliknya sendiri
            $pergerakan = $query->where('user_id', $user->id)
                ->orderBy('atd', 'asc')
                ->get();
        }

        // Ambil semua admin untuk dropdown filter
        $admins = User::whereHas('hak_akses', function($q) {
            $q->where('nama_hak_akses', 'Admin');
        })->get();

        $data = [
            'title' => 'Data Pergerakan Kapal',
            'menuPergerakan' => 'active',
            'pergerakan' => $pergerakan,
            'user' => $user,
            'admins' => $admins,
        ];

        return view('pergerakan/index', $data);
    }

    public function create()
    {
        $data = array(
            'title' => 'Tambah Data Pergerakan Kapal',
            'menuPergerakan' => 'active',
        );

        return view('pergerakan/create',$data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'ship_name'   => 'required|string|max:255',
            'grt'         => 'required|numeric',
            'dwt'         => 'nullable|numeric',
            'flag'        => 'required|string|max:100',
            'principal'   => 'required|string|max:255',
            'ata'         => 'required|date',
            'last_port'   => 'required|string|max:255',
            'atd'         => 'nullable|date|after_or_equal:ata',
            'next_port'   => 'nullable|string|max:255',
            'activities'  => 'nullable|in:Discharge,Loading,Bunker',
            'jetty'       => 'nullable|in:Pertamina,Pelindo',
            'cargo'       => 'nullable|string|max:255',
            'status'      => 'required|in:CMP,Pihak Ketiga,Tugboat',
        ], [
            'ship_name.required'  => 'Nama kapal wajib diisi.',
            'grt.required'        => 'GRT wajib diisi.',
            'grt.numeric'         => 'GRT harus berupa angka.',
            'dwt.numeric'         => 'DWT harus berupa angka.',
            'flag.required'       => 'Bendera (Flag) wajib diisi.',
            'principal.required'  => 'Principal wajib diisi.',
            'ata.required'        => 'ATA wajib diisi.',
            'ata.date'            => 'ATA harus berupa tanggal yang valid.',
            'last_port.required'  => 'Last Port wajib diisi.',
            'atd.date'            => 'ATD harus berupa tanggal yang valid.',
            'atd.after_or_equal'  => 'Tanggal ATD harus sama atau setelah tanggal ATA.',
            'activities.in'       => 'Pilihan activities tidak valid.',
            'jetty.in'            => 'Pilihan jetty tidak valid.',
            'cargo.in'            => 'Pilihan cargo tidak valid.',
            'status.required'     => 'Status wajib dipilih.',
            'status.in'           => 'Pilihan status tidak valid.',
        ]);

        // Simpan data ke database
        Pergerakan::create([
            'user_id'    => auth()->id(),
            'ship_name'  => $request->ship_name,
            'grt'        => $request->grt,
            'dwt'        => $request->dwt,
            'flag'       => $request->flag,
            'principal'  => $request->principal,
            'ata'        => $request->ata,
            'last_port'  => $request->last_port,
            'atd'        => $request->atd,
            'next_port'  => $request->next_port,
            'activities' => $request->activities,
            'jetty'      => $request->jetty,
            'cargo'      => $request->cargo,
            'status'     => $request->status,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('pergerakan')->with('success', 'Data pergerakan kapal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = array(
            'title' => 'Edit Data Pergerakan Kapal',
            'menuPergerakan' => 'active',
            'pergerakan' => Pergerakan::findOrFail($id),
        );

        return view('pergerakan/edit',$data);
    }

    public function update(Request $request, $id)
    {
        // Ambil data hak_akses berdasarkan ID
        $pergerakan = Pergerakan::findOrFail($id);

        // Validasi input
        $request->validate([
            'ship_name'   => 'required|string|max:255',
            'grt'         => 'required|numeric',
            'dwt'         => 'nullable|numeric',
            'flag'        => 'required|string|max:100',
            'principal'   => 'required|string|max:255',
            'ata'         => 'required|date',
            'last_port'   => 'required|string|max:255',
            'atd'         => 'nullable|date|after_or_equal:ata',
            'next_port'   => 'nullable|string|max:255',
            'activities'  => 'nullable|in:Discharge,Loading,Bunker',
            'jetty'       => 'nullable|in:Pertamina,Pelindo',
            'cargo'       => 'nullable|string|max:255',
            'status'      => 'required|in:CMP,Pihak Ketiga,Tugboat',
        ], [
            'ship_name.required'  => 'Nama kapal wajib diisi.',
            'grt.required'        => 'GRT wajib diisi.',
            'grt.numeric'         => 'GRT harus berupa angka.',
            'dwt.numeric'         => 'DWT harus berupa angka.',
            'flag.required'       => 'Bendera (Flag) wajib diisi.',
            'principal.required'  => 'Principal wajib diisi.',
            'ata.required'        => 'ATA wajib diisi.',
            'ata.date'            => 'ATA harus berupa tanggal yang valid.',
            'last_port.required'  => 'Last Port wajib diisi.',
            'atd.date'            => 'ATD harus berupa tanggal yang valid.',
            'atd.after_or_equal'  => 'Tanggal ATD harus sama atau setelah tanggal ATA.',
            'activities.in'       => 'Pilihan activities tidak valid.',
            'jetty.in'            => 'Pilihan jetty tidak valid.',
            'cargo.in'            => 'Pilihan cargo tidak valid.',
            'status.required'     => 'Status wajib dipilih.',
            'status.in'           => 'Pilihan status tidak valid.',
        ]);

        // Update data
        $pergerakan->update([
            'ship_name'  => $request->ship_name,
            'grt'        => $request->grt,
            'dwt'        => $request->dwt,
            'flag'       => $request->flag,
            'principal'  => $request->principal,
            'ata'        => $request->ata,
            'last_port'  => $request->last_port,
            'atd'        => $request->atd,
            'next_port'  => $request->next_port,
            'activities' => $request->activities,
            'jetty'      => $request->jetty,
            'cargo'      => $request->cargo,
            'status'     => $request->status,            
        ]);

        if ($request->origin === 'detail') {
            return redirect()->route('pergerakanDetail', $id)
                            ->with('success', 'Data pergerakan kapal berhasil diperbarui.');
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->route('pergerakan')->with('success', 'Data pergerakan kapal berhasil diperbarui.');
    }

    public function detail(Request $request)
    {
        $user = auth()->user(); 
        $query = Pergerakan::query();

        if (auth()->user()->hak_akses && in_array(auth()->user()->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            if ($request->has('user_id') && $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }
  
        $pergerakan = $query->orderBy('ata', 'asc')->get();

        $data = [
            'title' => 'Detail Data Pergerakan Kapal',
            'menuPergerakan' => 'active',
            'pergerakan' => $pergerakan,
        ];

        return view('pergerakan/detail', $data);
    }

    public function excel(Request $request)
    {
        $selectedMonth = Carbon::parse($request->bulan);
        $today = Carbon::today();
        $endOfCurrentMonth = $today->copy()->endOfMonth();

        // Jika bulan yang dipilih adalah bulan berjalan, tetapi belum akhir bulan maka ditolak
        if ($selectedMonth->format('Y-m') === $today->format('Y-m') && !$today->isSameDay($endOfCurrentMonth)) {
            return back()->with('error', 'Laporan bulan ini hanya dapat dicetak pada tanggal akhir bulan.');
        }

        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $bulan = Carbon::parse($request->bulan)->format('m');
        $tahun = Carbon::parse($request->bulan)->format('Y');
        $user = auth()->user();

        $query = Pergerakan::query();
        $id_port = null;
        $nama_pelabuhan = '-';

        // Filter user
        if (in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $query->where('user_id', $request->user_id);

                // Ambil nama pelabuhan dari user yang dipilih
                $admin = User::with('port')->find($request->user_id);
                $nama_pelabuhan = strtoupper($admin->port->port ?? '-');
                $id_port = $admin->port_id ?? null;
            } else {
                // Jika pilih "Semua Port"
                $nama_pelabuhan = 'JATIMBALINUS';
                $id_port = null;
            }
        } else {
            $query->where('user_id', $user->id);
            
            // Ambil nama pelabuhan dari user yg login 
            $nama_pelabuhan = strtoupper($user->port->port ?? '-');
            $id_port = $user->port_id ?? null;
        }

        // Filter bulan dan tahun berdasarkan kolom atd
        $query->whereNotNull('atd')
            ->whereMonth('atd', $bulan)
            ->whereYear('atd', $tahun);

        // Ambil data
        $pergerakan = $query->orderBy('atd', 'asc')->get();

        // Menambahkan periode
        $periode = Carbon::parse($request->bulan)->translatedFormat('F Y');

        $filename = 'DataPergerakanKapal_' . now()->format('d-m-Y_H-i-s') . '.xlsx';

        // Kirim semua variabel yang diperlukan
        return Excel::download(new PergerakanExport($nama_pelabuhan, $pergerakan, $bulan, $tahun, $periode), $filename);
    }

    public function pdf(Request $request)
    {
        $selectedMonth = Carbon::parse($request->bulan);
        $today = Carbon::today();
        $endOfCurrentMonth = $today->copy()->endOfMonth();

        // Jika bulan yang dipilih adalah bulan berjalan, tetapi belum akhir bulan maka ditolak
        if ($selectedMonth->format('Y-m') === $today->format('Y-m') && !$today->isSameDay($endOfCurrentMonth)) {
            return back()->with('error', 'Laporan bulan ini hanya dapat dicetak pada tanggal akhir bulan.');
        }

        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $bulan = Carbon::parse($request->bulan)->format('m');
        $tahun = Carbon::parse($request->bulan)->format('Y');
        $user = auth()->user();

        $query = Pergerakan::query();
        $id_port = null;
        $nama_pelabuhan = '-';

        // Tentukan port dan nama pelabuhan
        if (in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            if ($request->filled('user_id') && $request->user_id !== 'all') {
                // Filter port tertentu
                $query->where('user_id', $request->user_id);

                $admin = User::with('port')->find($request->user_id);
                $nama_pelabuhan = str($admin->port->port ?? '-');
                $id_port = $admin->port_id ?? null;
            } else {
                // Semua port
                $nama_pelabuhan = 'Jatimbalinus';
                $id_port = null;
            }
        } else {
            // User biasa
            $query->where('user_id', $user->id);
            $nama_pelabuhan = str($user->port->port ?? '-');
            $id_port = $user->port_id ?? null;
        }

        // Filter bulan dan tahun berdasarkan kolom atd
        $query->whereNotNull('atd')
            ->whereMonth('atd', $bulan)
            ->whereYear('atd', $tahun);

        $pergerakan = $query->orderBy('atd', 'asc')->get();
        $periode = Carbon::parse($request->bulan)->translatedFormat('F Y');
        $filename = 'DataPergerakanKapal_' . now()->format('d-m-Y_H-i-s') . '.pdf';
        $tanggal_akhir_display = Carbon::parse($request->bulan)->endOfMonth()->translatedFormat('d F Y'); // untuk tampilan
        $tanggal_akhir = Carbon::parse($request->bulan)->endOfMonth()->format('Y-m-d'); // untuk query database

        // Tentukan TTD Pegawai dari tabel ttd
        $nama_pegawai = '-';
        $ttd_pegawai = null;

        if ($id_port) {
            $hakAksesAdmin = HakAkses::where('nama_hak_akses', 'Admin')->first();
            
            // Jika port spesifik maka ambil TTD berdasarkan port_id
           $ttd = Ttd::where('hak_akses_id', $hakAksesAdmin->id ?? null)
                        ->where('port_id', $id_port)
                        ->whereDate('created_at', '<=', $tanggal_akhir)
                        ->where(function ($q) use ($tanggal_akhir) {
                            $q->where('isarsip', false)
                            ->orWhereDate('updated_at', '>', $tanggal_akhir);
                        })
                        ->first();

            $nama_pegawai = $ttd?->nama ?? '-';
            $ttd_pegawai = $ttd?->ttd_path ?? null;

        } else {
            // Semua port maka ambil TTD berdasarkan hak_akses = Sekretaris
            $hakAksesSekretaris = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();

            $ttd = Ttd::where('hak_akses_id', $hakAksesSekretaris->id ?? null)
                        ->whereDate('created_at', '<=', $tanggal_akhir)
                        ->where(function ($q) use ($tanggal_akhir) {
                            $q->where('isarsip', false)
                            ->orWhereDate('updated_at', '>', $tanggal_akhir);
                        })
                        ->first();

            $nama_pegawai = $ttd?->nama ?? '-';
            $ttd_pegawai = $ttd?->ttd_path ?? null;
        }

        $data = [
            'nama_pelabuhan' => $nama_pelabuhan,
            'pergerakan' => $pergerakan,
            'periode' => $periode,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
            'nama_pegawai' => $nama_pegawai,
            'ttd_pegawai' => $ttd_pegawai,
            'tanggal_akhir' => $tanggal_akhir,
            'tanggal_akhir_display' => $tanggal_akhir_display,
        ];

        $pdf = PDF::loadView('pergerakan/pdf', $data)
                ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function kedatangan()
    {
        $user = auth()->user(); // ambil user login
        $filename = now()->format('d-m-Y_H-i-s');
        $bulan = now()->month;
        $tahun = now()->year;

        // filter per bulan dan tahun ini
        $query = Pergerakan::whereYear('atd', $tahun)
            ->whereMonth('atd', $bulan)
            ->orderBy('ata', 'desc');

        // Jika bukan sekretaris, hanya tampilkan data milik user itu
        if (!in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            $query->where('user_id', $user->id);
        }

        $pergerakan = $query->get();

        $nama_pelabuhan = 'Jatimbalinus'; // bisa juga dinamis dari user login
        $periode = now()->translatedFormat('F Y'); // contoh: November 2025

        // Ambil TTD Sekretaris
        $sekretaris = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();
        $ttd = Ttd::where('hak_akses_id', $sekretaris->id ?? null)
            ->where('isarsip', false)
            ->first();

        $data = [
            'pergerakan' => $pergerakan,
            'nama_pelabuhan' => $nama_pelabuhan,
            'periode' => $periode,
            'tanggal_akhir_display' => now()->format('d F Y'),
            'nama_pegawai' => $ttd->nama ?? '-',
            'ttd_pegawai' => $ttd->ttd_path ?? null,
        ];

        $pdf = \PDF::loadView('pergerakan.kedatangan', $data);
        return $pdf->setPaper('a4', 'landscape')->stream('Pergerakan_Kapal_' . $filename . '.pdf');
    }

}