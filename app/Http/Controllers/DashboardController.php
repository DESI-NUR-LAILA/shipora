<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HakAkses;
use App\Models\Port;
use App\Models\Pergerakan;
use App\Models\Laporan;
use App\Models\Cuti;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Bulan & Tahun sekarang
        $bulan = Carbon::now()->format('m');
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));

        // Jumlah Pergerakan Kapal
        if (in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            // Melihat semua pergerakan kapal bulan ini
            $jumlahPergerakan = Pergerakan::whereYear('atd', $tahun)
                ->whereMonth('atd', $bulan)
                ->count();
        } else {
            // Admin hanya melihat pergerakan kapal yang dia input sendiri
            $jumlahPergerakan = Pergerakan::where('user_id', $user->id)
                ->whereYear('atd', $tahun)
                ->whereMonth('atd', $bulan)
                ->count();
        }

        // Jumlah User
        $jumlahUser = User::count();

        // Jumlah Admin
        $jumlahAdmin = User::whereHas('hak_akses', function($query) {
            $query->where('nama_hak_akses', 'Admin');
        })->count();

        // Jumlah Port
        $jumlahPort = Port::count();

        // Jumlah Cuti
        $jumlahCuti = [];

        if ($user->hak_akses->nama_hak_akses === 'Supervisor') {
            $jumlahCuti = Cuti::whereIn('status', ['pending', 'diketahui', 'disetujui'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', $tahun)
                ->count();
        } elseif ($user->hak_akses->nama_hak_akses === 'Admin') {
            $jumlahCuti = Cuti::whereHas('pegawai', function ($q) use ($user) {
                    $q->where('port_id', $user->port_id);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', $tahun)
                ->count();
        } elseif ($user->hak_akses->nama_hak_akses === 'PIC') {
            $jumlahCuti = Cuti::whereIn('status', ['diketahui', 'disetujui', 'ditolak'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', $tahun)
                ->count();
        }

        // Chart Laporan
        $laporanStatus = [];

        if ($user->hak_akses->nama_hak_akses === 'Sekretaris') {
            // Sekretaris hanya melihat laporan dengan status dikirim & disetujui
            $statusList = ['dikirim', 'disetujui'];
        } elseif (in_array($user->hak_akses->nama_hak_akses, ['HOA', 'Supervisor'])) {
            // HOA dan Supervisor hanya melihat laporan dengan status disetujui
            $statusList = ['disetujui'];
        } else {
            // Hak akses lain melihat semua status
            $statusList = ['draft', 'dikirim', 'ditolak', 'disetujui'];
        }

        foreach ($statusList as $status) {
            $laporanQuery = Laporan::where('status', $status)
                ->whereHas('pergerakan', function($q) use ($bulan, $tahun) {
                    $q->whereYear('atd', $tahun)
                    ->whereMonth('atd', $bulan);
                });

            // Jika bukan Sekretaris atau HOA, filter laporan milik sendiri
            if (!in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
                $laporanQuery->where('user_id', $user->id);
            }

            $laporanStatus[$status] = $laporanQuery
                ->distinct('pergerakan_id')
                ->count('pergerakan_id');
        }

        // Chart Surat Cuti
        $cutiStatus = [];
        $cutiStatusList = ['pending','diketahui','disetujui','ditolak'];

        foreach ($cutiStatusList as $status) {
            $cutiQuery = Cuti::where('status', $status)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan);

            if ($user->hak_akses->nama_hak_akses === 'Sekretaris') {
                // Sekretaris hanya melihat status disetujui
                if ($status !== 'disetujui') {
                    $cutiStatus[$status] = 0;
                    continue;
                }
            } elseif ($user->hak_akses->nama_hak_akses === 'Supervisor') {
                // Supervisor hanya melihat status pending, diketahui, dan disetujui
                $statusYangBoleh = ['pending', 'diketahui', 'disetujui'];
                if (!in_array($status, $statusYangBoleh)) {
                    $cutiStatus[$status] = 0;
                    continue;
                }
            } elseif ($user->hak_akses->nama_hak_akses === 'PIC') {
                // Supervisor hanya melihat status pending, diketahui, dan disetujui
                $statusYangBoleh = ['diketahui', 'disetujui', 'ditolak'];
                if (!in_array($status, $statusYangBoleh)) {
                    $cutiStatus[$status] = 0;
                    continue;
                }
            } else {
                // Admin melihat data dari port yang sama
                $cutiQuery->whereHas('pegawai', function($q) use ($user) {
                    $q->where('port_id', $user->port_id);
                });
            }

            $cutiStatus[$status] = $cutiQuery->count();
        }
        
        // Jumlah Pegawai
        $jumlahPegawai = Pegawai::where('port_id', $user->port_id)->count();

        // ==============================
        // CHART TREND DOKUMEN PER BULAN
        // ==============================

        $trendChart = [
            'cmp' => array_fill(1, 12, 0),
            'pihak_ketiga' => array_fill(1, 12, 0),
            'tugboat' => array_fill(1, 12, 0),
        ];

        // Hanya role tertentu yang bisa lihat chart
        if (in_array($user->hak_akses->nama_hak_akses, ['Admin', 'Sekretaris', 'Supervisor', 'HOA'])) {

            $laporanQuery = Laporan::with('pergerakan')
                ->whereNotNull('path_file')
                ->where('status', 'disetujui')
                ->whereHas('pergerakan', function ($q) use ($tahun) {
                    $q->whereYear('atd', $tahun);
                });

            // Jika Admin → hanya data milik sendiri
            if ($user->hak_akses->nama_hak_akses === 'Admin') {
                $laporanQuery->where('user_id', $user->id);
            }

            $laporans = $laporanQuery->get();

            foreach ($laporans as $lap) {
                if (!$lap->pergerakan || !$lap->pergerakan->atd) continue;

                $bulanIndex = Carbon::parse($lap->pergerakan->atd)->month;
                $statusKapal = strtolower($lap->pergerakan->status);

                if ($statusKapal === 'cmp') {
                    $trendChart['cmp'][$bulanIndex]++;
                } elseif ($statusKapal === 'pihak ketiga') {
                    $trendChart['pihak_ketiga'][$bulanIndex]++;
                } elseif ($statusKapal === 'tugboat') {
                    $trendChart['tugboat'][$bulanIndex]++;
                }
            }
        }

        // Format untuk chart (biar rapi urut Jan–Des)
        $chartLabels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $chartCMP = array_values($trendChart['cmp']);
        $chartPihakKetiga = array_values($trendChart['pihak_ketiga']);
        $chartTugboat = array_values($trendChart['tugboat']);

        // 6. Data untuk dikirim ke view
        $data = [
            'title' => 'Dashboard',
            'menuDashboard' => 'active',
            'jumlahUser' => $jumlahUser,
            'jumlahAdmin' => $jumlahAdmin,
            'jumlahPort' => $jumlahPort,
            'jumlahPergerakan' => $jumlahPergerakan,
            'jumlahCuti' => $jumlahCuti,
            'jumlahPegawai' => $jumlahPegawai,
            'laporanDraft' => $laporanStatus['draft'] ?? 0,
            'laporanDikirim' => $laporanStatus['dikirim'] ?? 0,
            'laporanDisetujui' => $laporanStatus['disetujui'] ?? 0,
            'laporanDitolak' => $laporanStatus['ditolak'] ?? 0,
            'cutiPending' => $cutiStatus['pending'] ?? 0,
            'cutiDiketahui' => $cutiStatus['diketahui'] ?? 0,
            'cutiDisetujui' => $cutiStatus['disetujui'] ?? 0,
            'cutiDitolak' => $cutiStatus['ditolak'] ?? 0,
            'chartLabels' => $chartLabels,
            'chartCMP' => $chartCMP,
            'chartPihakKetiga' => $chartPihakKetiga,
            'chartTugboat' => $chartTugboat,
            'tahun' => $tahun,
        ];

        return view('dashboard', $data);
    }

    public function pdf(Request $request)
    {
        $tahun = $request->tahun ?? Carbon::now()->year;

        $laporans = Laporan::with('pergerakan')
            ->where('status', 'disetujui')
            ->whereNotNull('path_file')
            ->whereHas('pergerakan', function ($q) use ($tahun) {
                $q->whereYear('atd', $tahun);
            })
            ->get();

        $data = [];

        foreach ($laporans as $lap) {
            if (!$lap->pergerakan || !$lap->pergerakan->atd) continue;

            $bulan = Carbon::parse($lap->pergerakan->atd)->month;
            $status = strtolower($lap->pergerakan->status);
            $tanggalATD = Carbon::parse($lap->pergerakan->atd)->format('d-m-Y');
            $kapal = $lap->pergerakan->ship_name . '_' . $tanggalATD;

            if (!isset($data[$status][$kapal])) {
                $data[$status][$kapal] = array_fill(1, 12, 0);
            }

            $data[$status][$kapal][$bulan]++;
        }

        $pdf = Pdf::loadView('pdf', [
            'data' => $data,
            'tahun' => $tahun
        ]);
        return $pdf->setPaper('a4', 'landscape')
           ->stream('TrendDokumen_'.$tahun.'.pdf');
    }
}