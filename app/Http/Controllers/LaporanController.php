<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\PhpWord;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use setasign\Fpdi\FpdiException;
use setasign\Fpdf\Fpdf;

use App\Models\User;
use App\Models\Laporan;
use App\Models\Pergerakan;
use App\Models\Pegawai;
use App\Models\Ttd;
use App\Models\HakAkses;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $hak_akses = $user->hak_akses->nama_hak_akses;

        // pergerakan
        $queryPergerakan = Pergerakan::query();

        if (in_array($hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {
            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $queryPergerakan->where('user_id', $request->user_id);
            }
        } else {
            $queryPergerakan->where('user_id', $user->id);
        }

        $pergerakan = $queryPergerakan->orderBy('atd', 'asc')->get();

        // laporan
        $laporanQuery = Laporan::with('pergerakan');

        // Filter berdasarkan status sesuai hak akses
        if ($hak_akses === 'Sekretaris') {
            $laporanQuery->whereIn('status', ['dikirim', 'disetujui']);
        } elseif (in_array($hak_akses, ['Supervisor', 'HOA'])) {
            $laporanQuery->where('status', 'Disetujui');
        }

        // Filter user
        if (in_array($hak_akses, ['Sekretaris', 'Supervisor', 'HOA'])) {
            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $laporanQuery->where('user_id', $request->user_id);
            }
        } else {
            $laporanQuery->where('user_id', $user->id);
        }

        // Ambil laporan dengan group untuk ambil yang terbaru
        $laporan = $laporanQuery
            ->selectRaw('MAX(id) as id')
            ->groupBy('pergerakan_id')
            ->get()
            ->map(fn($item) => Laporan::with('pergerakan')->find($item->id))
            ->sortByDesc(fn($lap) => $lap->pergerakan->atd)
            ->values();

        // admin list untuk filter
        $admins = User::whereHas('hak_akses', fn($q) =>
            $q->where('nama_hak_akses', 'Admin')
        )->get();

        return view('laporan/index', [
            'title' => 'Data Laporan',
            'menuLaporan' => 'active',
            'laporan' => $laporan,
            'admins' => $admins,
            'pergerakan' => $pergerakan,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $pergerakan_id = $request->pergerakan_id;

        if ($pergerakan_id) {
            // Kalau datang dari halaman detail, ambil semua pergerakan milik user yang login
            $pergerakan = Pergerakan::where('user_id', $user->id)->get();
        } else {
            // Kalau dari tombol tambah di index, hanya tampilkan pergerakan milik user login
            // dan yang belum ada laporan
            $pergerakan = Pergerakan::where('user_id', $user->id)
                ->whereNotIn('id', function ($query) {
                    $query->select('pergerakan_id')->from('laporans');
                })
                ->get();
        }

        return view('laporan/create', [
            'title' => 'Tambah Laporan',
            'menuLaporan' => 'active',
            'pergerakan' => $pergerakan,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pergerakan_id' => 'required|exists:pergerakans,id',
            'jenis_file' => 'required|array',
            'jenis_file.*' => 'required|string|max:255',
            'path_file' => 'nullable|array',
            'path_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,xls',
            'no_resi' => 'nullable|string|max:255',
        ]);

        $files = $request->file('path_file', []);
        $jenisFiles = $request->jenis_file;

        // Ambil semua laporan pergerakan yang terkait
        $laporanPergerakan = Laporan::where('pergerakan_id', $request->pergerakan_id)->get();

        $semuaDikirim = $laporanPergerakan->count() > 0 && $laporanPergerakan->every(fn($lap) => $lap->status === 'dikirim');
        $adaDitolak = $laporanPergerakan->contains(fn($lap) => $lap->status === 'ditolak');

        // Jika semua dikirim atau ada yang ditolak maka ubah semua jadi draft
        if ($semuaDikirim || $adaDitolak) {
            Laporan::where('pergerakan_id', $request->pergerakan_id)
                ->update(['status' => 'draft']);
        }

        foreach ($jenisFiles as $index => $jenisFile) {
            $file = $files[$index] ?? null;
            if (!$file) continue;

            $existing = Laporan::where('pergerakan_id', $request->pergerakan_id)
                ->where('jenis_file', $jenisFile)
                ->first();

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
            $timestamp = now()->format('dmY_His');
            $extension = $file->getClientOriginalExtension();
            $namaFile = "{$safeName}_{$jenisFile}_{$timestamp}.{$extension}";

            $path = $file->storeAs('laporan', $namaFile, 'public');
            $finalPath = $path;

            // Konversi Word ke PDF
            if (in_array(strtolower($extension), ['doc', 'docx'])) {
                try {
                    $phpWord = IOFactory::load($file->getRealPath());
                    $pdfName = "{$safeName}_{$jenisFile}_{$timestamp}.pdf";
                    $pdfPath = storage_path("app/public/laporan/{$pdfName}");

                    Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
                    Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));

                    $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
                    $pdfWriter->save($pdfPath);

                    Storage::disk('public')->delete($path);
                    $finalPath = "laporan/{$pdfName}";
                } catch (\Exception $e) {
                    \Log::error('Konversi Word ke PDF gagal: ' . $e->getMessage());
                }
            }

            // Update existing tanpa ubah status (karena status sudah di-handle di atas)
            if ($existing) {
                if ($existing->path_file) {
                    Storage::disk('public')->delete($existing->path_file);
                }

                $existing->update([
                    'nama_file' => $safeName,
                    'path_file' => $finalPath,
                    'no_resi' => $request->no_resi,
                    'user_id' => auth()->id(),
                ]);
            } else {
                // Buat laporan baru, status tetap draft
                Laporan::create([
                    'pergerakan_id' => $request->pergerakan_id,
                    'jenis_file' => $jenisFile,
                    'nama_file' => $safeName,
                    'path_file' => $finalPath,
                    'no_resi' => $request->no_resi,
                    'status' => 'draft',
                    'user_id' => auth()->id(),
                ]);
            }
        }

        // Update no_resi untuk semua laporan pergerakan ini
        if ($request->no_resi) {
            Laporan::where('pergerakan_id', $request->pergerakan_id)
                ->update(['no_resi' => $request->no_resi]);
        }

        return redirect()
            ->route('laporan', ['id' => $request->pergerakan_id])
            ->with('success', 'Laporan berhasil ditambahkan. Semua laporan terkait telah diubah menjadi draft.');
    }

    public function getJenisFile($pergerakan_id)
    {
        $pergerakan = Pergerakan::findOrFail($pergerakan_id);

        $jenisFileOptions = match (strtolower($pergerakan->status)) {
            'cmp' => [
                'SOD Veson', 'SOD Manual', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Requisition List Fresh Water', 'Surat Persetujuan Berlayar', 'Time Sheet, Bill of Lading'
            ],
            'pihak ketiga' => [
                'SOD', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Surat Persetujuan Berlayar', 'Sertifikat Port Health Quarantine Clearance', 'Surat Persetujuan Olah Gerak',
                'Pemberitahuan Kedatangan Kapal', 'Laporan Kedatangan / Keberangkatan Kapal', 'Crew List', 'Time Sheet, Bill of Lading',
                'Surat Penunjukan Keagenan', 'Estimate Port Disbursement Account', 'Bukti Bayar'
            ],
            'tugboat' => [
                'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
            ],
            default => [],
        };

        // Ambil semua laporan per kapal
        $laporan = Laporan::where('pergerakan_id', $pergerakan_id)->get();

        // Pisahkan berdasarkan apakah sudah upload atau belum
        $uploaded = $laporan->whereNotNull('path_file')->pluck('jenis_file')->toArray();
        $notUploaded = $laporan->whereNull('path_file')->pluck('jenis_file')->toArray();

        // Cari jenis file yang belum ada sama sekali di database
        $missingJenis = array_diff($jenisFileOptions, $laporan->pluck('jenis_file')->toArray());

        // Gabungkan yang belum ada dan yang path_file-nya null
        $available = array_values(array_unique(array_merge($notUploaded, $missingJenis)));

        return response()->json([
            'available' => $available,
            'uploaded' => $uploaded,
        ]);
    }

    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        $pergerakan = Pergerakan::orderBy('ship_name')->get();

        // Jika laporan yang dipilih bukan draft, ubah semua laporan dengan pergerakan_id yang sama jadi draft
        if ($laporan->status !== 'draft') {
            Laporan::where('pergerakan_id', $laporan->pergerakan_id)
                ->update(['status' => 'draft']);
        }

        // Ambil semua jenis file yang SUDAH di-upload untuk pergerakan ini
        $uploadedJenisFiles = Laporan::where('pergerakan_id', $laporan->pergerakan_id)
            ->whereNotNull('path_file')
            ->pluck('jenis_file')
            ->toArray();

        $jenisFileOptions = $uploadedJenisFiles;

        return view('laporan/edit', [
            'title' => 'Edit Laporan',
            'menuLaporan' => 'active',
            'laporan' => $laporan,
            'pergerakan' => $pergerakan,
            'jenisFileOptions' => $jenisFileOptions,
        ]);
    }

    public function getJenisFileEdit($id)
    {
        $laporan = Laporan::findOrFail($id);
        $pergerakanId = $laporan->pergerakan_id;

        // Ambil semua laporan dengan pergerakan_id yang sama
        $uploadedLaporans = Laporan::where('pergerakan_id', $pergerakanId)
            ->whereNotNull('path_file')
            ->get(['jenis_file', 'path_file']);

        // Hanya menampilkan jenis file yang sudah diupload
        $uploaded = $uploadedLaporans->map(function ($item) {
            return [
                'jenis_file' => $item->jenis_file,
                'path_file' => $item->path_file
                    ? asset('storage/' . $item->path_file)
                    : null
            ];
        });

        return response()->json([
            'uploaded' => $uploaded
        ]);
    }

    public function update(Request $request, $id)
    {
        $laporanUtama = Laporan::findOrFail($id);

        $request->validate([
            'pergerakan_id' => 'required|exists:pergerakans,id',
            'jenis_file' => 'required|array',
            'jenis_file.*' => 'string|max:255',
            'path_file' => 'nullable|array',
            'path_file.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,xls',
            'no_resi' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,dikirim,ditolak,disetujui',
            'komentar' => 'nullable|string',
            'hapus_file' => 'nullable|array',
        ]);

        $jenisFiles = $request->jenis_file;
        $files = $request->file('path_file', []);
        $hapusFiles = $request->hapus_file ?? [];

        foreach ($jenisFiles as $index => $jenisFile) {
            $laporan = Laporan::where('pergerakan_id', $request->pergerakan_id)
                ->where('jenis_file', $jenisFile)
                ->first();

            if (!$laporan) continue;

            $file = $files[$index] ?? null;
            $finalPath = $laporan->path_file;
            $namaFile = $laporan->nama_file;

            // Jika user centang "hapus file"
            if (in_array($jenisFile, $hapusFiles)) {
                if ($laporan->path_file) {
                    Storage::disk('public')->delete($laporan->path_file);
                }

                // Hapus record sekalian agar tidak muncul double (null & non-null)
                $laporan->delete();

                continue; // lanjut ke jenis file berikutnya
            }

            // Jika user upload file baru
            if ($file) {
                if ($laporan->path_file) {
                    Storage::disk('public')->delete($laporan->path_file);
                }

                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
                $timestamp = now()->format('dmY_His');
                $extension = strtolower($file->getClientOriginalExtension());
                $newFileName = "{$safeName}_{$jenisFile}_{$timestamp}.{$extension}";

                $path = $file->storeAs('laporan', $newFileName, 'public');
                $finalPath = $path;
                $namaFile = $safeName;

                // Konversi Word ke PDF
                if (in_array($extension, ['doc', 'docx'])) {
                    try {
                        $phpWord = IOFactory::load($file->getRealPath());
                        $pdfName = "{$safeName}_{$jenisFile}_{$timestamp}.pdf";
                        $pdfPath = storage_path("app/public/laporan/{$pdfName}");

                        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
                        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));

                        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
                        $pdfWriter->save($pdfPath);

                        // Hapus Word asli
                        Storage::disk('public')->delete($path);

                        // Simpan path PDF
                        $finalPath = "laporan/{$pdfName}";
                    } catch (\Exception $e) {
                        \Log::error('Konversi Word ke PDF gagal (update): ' . $e->getMessage());
                    }
                }
            }

            $updateData = [
                'nama_file' => $namaFile,
                'path_file' => $finalPath,
                'no_resi' => $request->no_resi,
                'status' => $request->status ?? $laporan->status,
            ];

            // Hanya update komentar jika dikirim (misal dari form tertentu)
            if ($request->filled('komentar')) {
                $updateData['komentar'] = $request->komentar;
            }

            $laporan->update($updateData);
        }

        return redirect()
            ->route('laporan')
            ->with('success', 'Laporan berhasil diperbarui (file baru dikonversi dan file lama dihapus jika dipilih).');
    }

    public function detail($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);

        // Ambil laporan yang punya file
        $laporan = Laporan::where('pergerakan_id', $id)
            ->whereNotNull('path_file')
            ->get();
        
        // Ambil laporan pertama (laporan utama) beserta relasi pergerakan
        $laporanUtama = Laporan::with('pergerakan')->where('pergerakan_id', $id)->first();

        // Ambil no_resi dari laporan pertama (kalau ada)
        $noResiExisting = Laporan::where('pergerakan_id', $id)
            ->whereNotNull('no_resi')
            ->value('no_resi');

        // Normalisasi status kapal untuk memudahkan perbandingan di Blade
        // "Pihak Ketiga" -> "pihak_ketiga", "CMP" -> "cmp", "Tugboat" -> "tugboat"
        $statusPergerakan = strtolower(str_replace(' ', '_', $pergerakan->status));

        // Daftar urutan file
        $urutanJenis = [
            'cmp' => [
                'SOD Veson', 'SOD Manual', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Requisition List Fresh Water', 'Surat Persetujuan Berlayar', 'Time Sheet, Bill of Lading'
            ],
            'pihak_ketiga' => [
                'SOD', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Surat Persetujuan Berlayar', 'Sertifikat Port Health Quarantine Clearance', 'Surat Persetujuan Olah Gerak',
                'Pemberitahuan Kedatangan Kapal', 'Laporan Kedatangan / Keberangkatan Kapal', 'Crew List', 'Time Sheet, Bill of Lading',
                'Surat Penunjukan Keagenan', 'Estimate Port Disbursement Account', 'Bukti Bayar'
            ],
            'tugboat' => [
                'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
            ]
        ];

        // Ambil urutan berdasarkan jenis kapal
        $urutan = $urutanJenis[$statusPergerakan] ?? [];

        // Urutkan koleksi laporan
        $laporan = $laporan->sortBy(function ($item) use ($urutan) {
            $index = array_search($item->jenis_file, $urutan);
            return $index !== false ? $index : PHP_INT_MAX;
        })->values();

        $masihAdaKomentar = !empty($laporanUtama->komentar);

        return view('laporan/detail', [
            'title' => 'Detail Laporan',
            'menuLaporan' => 'active',
            'laporan' => $laporan,
            'pergerakan' => $pergerakan,
            'noResiExisting' => $noResiExisting,
            'laporanUtama' => $laporanUtama,
            'statusPergerakan' => $statusPergerakan, // kirim ke Blade
            'masihAdaKomentar' => $masihAdaKomentar,
        ]);
    }

    public function komentar(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->komentar = $request->komentar;
        $laporan->save();

        return redirect()->back()->with('success', 'Komentar berhasil disimpan.');
    }

    public function verifikasiKomentar($id)
    {
        $laporan = Laporan::findOrFail($id);

        // Pastikan yang klik adalah Admin
        if (auth()->user()->hak_akses->nama_hak_akses !== 'Admin') {
            abort(403);
        }

        // Set komentar menjadi null (verifikasi)
        $laporan->update(['komentar' => null]);

        return back()->with('success', 'Komentar berhasil diverifikasi.');
    }

    public function updateStatus(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        $statusBaru = $request->input('status');

        // Cek apakah pergerakan adalah pihak ketiga
        $isPihakKetiga = strtolower($laporan->pergerakan->status) === 'pihak ketiga';

        // Jika pihak ketiga menginputkan no_resi WAJIB
        if ($statusBaru === 'dikirim' && $isPihakKetiga) {
            $request->validate([
                'no_resi' => 'required|string|max:50',
            ]);

            Laporan::where('pergerakan_id', $laporan->pergerakan_id)
                ->update([
                    'status' => $statusBaru,
                    'no_resi' => $request->input('no_resi'),
                ]);
        } 
        // Jika CMP / Tugboat → tidak perlu no_resi
        else {
            Laporan::where('pergerakan_id', $laporan->pergerakan_id)
                ->update(['status' => $statusBaru]);
        }

        return redirect()->route('laporan')
            ->with('success', 'Laporan ' . ($laporan->pergerakan->ship_name ?? '') . ' berhasil ' . $statusBaru . '.');
    }

    public function revisi(Request $request, $pergerakan_id)
    {
        // Validasi input alasan
        $request->validate([
            'alasan' => 'required|string'
        ]);

        // Pastikan user Sekretaris
        if (auth()->user()->hak_akses->nama_hak_akses !== 'Sekretaris') {
            abort(403, 'Akses ditolak');
        }

        // Ambil semua laporan berdasar pergerakan_id
        $laporanList = Laporan::where('pergerakan_id', $pergerakan_id)->get();

        if ($laporanList->isEmpty()) {
            return back()->with('error', 'Tidak ada laporan yang ditemukan.');
        }

        // 🔹 Ambil laporan **pertama** (yang paling awal)
        $laporanPertama = Laporan::where('pergerakan_id', $pergerakan_id)
            ->first();

        // Simpan alasan revisi ke kolom komentar
        $laporanPertama->komentar = $request->alasan;
        $laporanPertama->save();

        // 🔹 Ubah semua status menjadi draft
        Laporan::where('pergerakan_id', $pergerakan_id)
            ->update(['status' => 'Draft']);

        return back()->with('success', 'Laporan berhasil direvisi dan dikembalikan ke Draft.');
    }

    public function combine($id)
    {
        $laporan = Laporan::findOrFail($id);
        $status = strtolower($laporan->pergerakan->status);

        // Ambil semua laporan terkait pergerakan yang sama
        $laporanFiles = Laporan::where('pergerakan_id', $laporan->pergerakan_id)
            ->whereNotNull('path_file')
            ->get(['path_file', 'jenis_file']);

        if ($laporanFiles->isEmpty()) {
            return back()->with('error', 'Tidak ada file laporan untuk digabung.');
        }

        // Tentukan urutan file berdasarkan jenis kapal
        $orderMap = [
            'cmp' => [
                'SOD Veson', 'SOD Manual', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Requisition List Fresh Water', 'Surat Persetujuan Berlayar', 'Time Sheet, Bill of Lading'
            ],
            'pihak ketiga' => [
                'SOD', 'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
                'Kwitansi Karantina', 'Surat Persetujuan Berlayar', 'Sertifikat Port Health Quarantine Clearance', 'Surat Persetujuan Olah Gerak',
                'Pemberitahuan Kedatangan Kapal', 'Laporan Kedatangan / Keberangkatan Kapal', 'Crew List', 'Time Sheet, Bill of Lading',
                'Surat Penunjukan Keagenan', 'Estimate Port Disbursement Account', 'Bukti Bayar'
            ],
            'tugboat' => [
                'Nota Pandu, Tunda, Tambat', 'Pranota Pandu, Tunda, Tambat', 'Nota Fuel Surcharge', 'Pranota Fuel Surcharge',
                'Billing Labuh', 'Billing Rambu', 'Billing PUP9', 'Billing Mastercable', 'Billing Port Health Quarantine Clearance', 'Bukti Bayar Port Health Quarantine Clearance',
            ]
        ];

        // Ambil urutan sesuai status kapal (default: CMP)
        $priorityOrder = $orderMap[$status] ?? $orderMap['cmp'];

        // Urutkan laporan berdasarkan posisi jenis_file di array prioritas
        $sortedFiles = $laporanFiles->sortBy(function ($file) use ($priorityOrder) {
            $index = array_search($file->jenis_file, $priorityOrder);
            return $index === false ? PHP_INT_MAX : $index; // file tak dikenal taruh di belakang
        });

        // Buat nama file hasil gabungan
        $outputPath = 'combined_laporan/laporan_' .
            str_replace([' ', '/'], '_', $laporan->pergerakan->ship_name) . '_' .
            now()->format('Ymd_His') . '.pdf';

        $outputFullPath = storage_path('app/public/' . $outputPath);

        // Pastikan folder tujuan ada
        if (!file_exists(dirname($outputFullPath))) {
            mkdir(dirname($outputFullPath), 0777, true);
        }

        // Gabungkan file sesuai urutan
        $pdf = new Fpdi();

        foreach ($sortedFiles as $file) {
            $filePath = storage_path('app/public/' . $file->path_file);
            if (file_exists($filePath)) {
                try {
                    $pageCount = $pdf->setSourceFile($filePath);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($templateId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                    }
                } catch (\Exception $e) {
                    \Log::error("Gagal gabung file: {$file->path_file} ({$e->getMessage()})");
                }
            }
        }

        // Simpan hasil gabungan
        $pdf->Output($outputFullPath, 'F');

        // Tampilkan hasilnya
        return response()->file($outputFullPath);
    }

    public function excel(Request $request)
    {
        $selectedMonth = Carbon::parse($request->bulan);
        $today = Carbon::today();
        $endOfCurrentMonth = $today->copy()->endOfMonth();

        if ($selectedMonth->format('Y-m') === $today->format('Y-m') && !$today->isSameDay($endOfCurrentMonth)) {
            return back()->with('error', 'Laporan bulan ini hanya dapat dicetak pada tanggal akhir bulan.');
        }

        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $bulan = Carbon::parse($request->bulan)->format('m');
        $tahun = Carbon::parse($request->bulan)->format('Y');
        $user = auth()->user();

        $query = Laporan::with('pergerakan');
        $id_port = null;
        $nama_pelabuhan = '-';

        // Filter user/port
        if (in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {

            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $query->where('laporans.user_id', $request->user_id);
                $admin = User::with('port')->find($request->user_id);
                $nama_pelabuhan = strtoupper($admin->port->port ?? '-');
                $id_port = $admin->port_id ?? null;
            } else {
                $nama_pelabuhan = 'JATIMBALINUS';
            }

        } else {
            $query->where('laporans.user_id', $user->id);
            $nama_pelabuhan = strtoupper($user->port->port ?? '-');
            $id_port = $user->port_id ?? null;
        }

        // Filter berdasarkan bulan ATD
        $laporan = $query->whereHas('pergerakan', function ($q) use ($bulan, $tahun) {
                $q->whereNotNull('atd')
                ->whereMonth('atd', $bulan)
                ->whereYear('atd', $tahun);
            })
            ->orderByRelation('pergerakan.atd', 'asc')
            ->get()
            ->unique('pergerakan_id');

        $periode = Carbon::parse($request->bulan)->translatedFormat('F Y');
        $filename = 'DataLaporanKapal_' . now()->format('d-m-Y_H-i-s') . '.xlsx';

        return Excel::download(new LaporanExport($nama_pelabuhan, $laporan, $bulan, $tahun, $periode), $filename);
    }

    public function pdf(Request $request)
    {

        $selectedMonth = Carbon::parse($request->bulan);
        $today = Carbon::today();
        $endOfCurrentMonth = $today->copy()->endOfMonth();

        if ($selectedMonth->format('Y-m') === $today->format('Y-m') && !$today->isSameDay($endOfCurrentMonth)) {
            return back()->with('error', 'Laporan bulan ini hanya dapat dicetak pada tanggal akhir bulan.');
        }

        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $bulan = Carbon::parse($request->bulan)->format('m');
        $tahun = Carbon::parse($request->bulan)->format('Y');
        $user = auth()->user();

        // Mulai query laporan + relasi pergerakan
        $query = Laporan::with('pergerakan');

        $id_port = null;
        $nama_pelabuhan = '-';

        // Filter berdasarkan hak_akses
        if (in_array($user->hak_akses->nama_hak_akses, ['Sekretaris', 'HOA', 'Supervisor'])) {

            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $query->where('laporans.user_id', $request->user_id);

                $admin = User::with('port')->find($request->user_id);
                $nama_pelabuhan = strtoupper($admin->port->port ?? '-');
                $id_port = $admin->port_id ?? null;
            } else {
                $nama_pelabuhan = 'JATIMBALINUS';
            }

        } else {
            $query->where('laporans.user_id', $user->id);
            $nama_pelabuhan = strtoupper($user->port->port ?? '-');
            $id_port = $user->port_id ?? null;
        }

        // Filter laporan berdasarkan bulan & tahun ATD di relasi pergerakan
        $laporan = $query->whereHas('pergerakan', function ($q) use ($bulan, $tahun) {
            $q->whereNotNull('atd')
            ->whereMonth('atd', $bulan)
            ->whereYear('atd', $tahun);
        })
        ->with('pergerakan')
        ->get()
        ->sortBy('pergerakan.atd')
        ->unique('pergerakan_id'); // hanya ambil satu per pergerakan_id

        // Informasi tambahan
        $periode = Carbon::parse($request->bulan)->translatedFormat('F Y');
        $filename = 'DataLaporanKapal_' . now()->format('d-m-Y_H-i-s') . '.pdf';
        $tanggal_akhir_display = Carbon::parse($request->bulan)->endOfMonth()->translatedFormat('d F Y'); // untuk tampilan
        $tanggal_akhir = Carbon::parse($request->bulan)->endOfMonth()->format('Y-m-d'); // untuk query database

        // Tentukan TTD Pegawai dari tabel ttd
        $nama_pegawai = '-';
        $ttd_pegawai = null;

        if ($id_port) {
            $hakAksesAdmin = HakAkses::where('nama_hak_akses', 'Admin')->first();
            
            // Jika port spesifik → ambil TTD berdasarkan port_id
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
            // Semua port → ambil TTD berdasarkan hak_akses = Sekretaris
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

        // Kirim data ke view PDF
        $data = [
            'nama_pelabuhan' => $nama_pelabuhan,
            'laporan' => $laporan,
            'periode' => $periode,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
            'nama_pegawai' => $nama_pegawai,
            'ttd_pegawai' => $ttd_pegawai,
            'tanggal_akhir' => $tanggal_akhir,
            'tanggal_akhir_display' => $tanggal_akhir_display,
        ];

        $pdf = PDF::loadView('laporan/pdf', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }
}