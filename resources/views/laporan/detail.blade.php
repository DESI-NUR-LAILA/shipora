@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-eye mr-2"></i>
    {{ $title }}
</h1>

<div class="card mb-4">
    <div class="card-header bg-info">
        <a href="{{ route('laporan') }}" class="btn btn-sm btn-light text-info">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive mb-4">
            <table class="table table-sm table-striped align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 35%;">Nama Kapal</th>
                        <td class="font-weight-semibold">{{ $pergerakan->ship_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status Kapal</th>
                        <td>{{ $pergerakan->status }}</td>
                    </tr>
                    @php 
                        $laporanUtama = $laporan->first(); // Ambil laporan pertama 
                    @endphp 

                    @if ($statusPergerakan === 'pihak_ketiga')
                        <tr>
                            <th class="text-muted">No Resi</th>
                            <td>{{ $laporanUtama->no_resi ?? '-' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th class="text-muted">Status Laporan</th>
                        <td>
                            @if ($laporanUtama->status == 'draft')
                                <span class="badge badge-secondary">
                                    <i class="fas fa-pencil-alt"></i> Draft
                                </span>
                            @elseif ($laporanUtama->status == 'dikirim')
                                <span class="badge badge-primary">
                                    <i class="fas fa-paper-plane"></i> Dikirim
                                </span>
                            @elseif ($laporanUtama->status == 'disetujui')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Disetujui
                                </span>
                            @elseif ($laporanUtama->status == 'ditolak')
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </span>
                            @else
                                <span class="badge badge-light">
                                    <i class="fas fa-question-circle"></i> {{ ucfirst($laporanUtama->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Laporan SOD</th>
                        <td>
                            @php
                                // Pastikan $laporan berupa koleksi
                                $laporanCollection = is_iterable($laporan) ? collect($laporan) : collect([$laporan]);

                                // Ambil semua file yang ada di kolom path_file
                                $fileLaporan = $laporanCollection->pluck('path_file')->filter();
                            @endphp

                            @if ($fileLaporan->isEmpty())
                                <span class="badge badge-secondary">Belum ada laporan yang dikirim</span>
                            @else
                                <a href="{{ route('laporanCombine', $laporan->first()->id) }}" 
                                class="badge badge-info" target="_blank" style="text-decoration: none;">
                                    <i class="fas fa-file-pdf"></i> Lihat Laporan SOD
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Header daftar file dan tombol tambah --}}
        @if(Auth::user()->hak_akses->nama_hak_akses === 'Admin')

            @php
                // Ambil semua laporan pergerakan_id yang sama
                $laporanPergerakan = $laporan->groupBy('pergerakan_id');
            @endphp

            @foreach($laporanPergerakan as $pergerakanId => $laporanGroup)
                @php
                    $laporanDikirim = $laporanGroup->firstWhere('status', 'dikirim');
                    $semuaDikirim = $laporanGroup->count() > 0 && $laporanGroup->every(fn($lap) => $lap->status === 'dikirim');
                    $semuaDisetujui = $laporanGroup->count() > 0 && $laporanGroup->every(fn($lap) => $lap->status === 'disetujui');
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold mb-0">Daftar File Laporan</h5>
                    
                    {{-- Menyembunyikan tombol jika semua disetujui --}}
                    @if(!$semuaDisetujui)
                        @if($semuaDikirim)
                            {{-- Semua dikirim → tampil modal peringatan --}}
                            <button class="btn btn-sm btn-outline-info"
                                data-toggle="modal"
                                data-target="#modalTambah{{ $pergerakanId }}"
                                title="Tambah Jenis File">
                                <i class="fas fa-plus mr-1"></i> Tambah Jenis File
                            </button>
                        @else
                            {{-- Masih ada draft maka langsung ke form create --}}
                            <a href="{{ route('laporanCreate', $pergerakanId) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-plus mr-1"></i> Tambah Jenis File
                            </a>
                        @endif
                    @endif
                </div>

                 @if($semuaDikirim && !$semuaDisetujui)
                    {{-- Modal untuk peringatan --}}
                    <div class="modal fade" id="modalTambah{{ $pergerakanId }}" tabindex="-1"
                        aria-labelledby="modalTambahLabel{{ $pergerakanId }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title">Konfirmasi Tambah Jenis File</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Laporan <strong>sudah dikirim</strong>
                                        Menambahkan jenis file baru akan mengubah <strong>status laporan menjadi Draft</strong>.
                                    </div>
                                    <p>Apakah Anda yakin ingin melanjutkan proses tambah jenis file ini?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Tutup
                                    </button>
                                    <a href="{{ route('laporanCreate', $pergerakanId) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-plus"></i> Tambah Jenis File
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @endforeach
        @endif

        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Jenis File</th>
                        <th>Nama File</th>
                        <th>Path File</th>
                        {{-- Hanya tampil untuk hak_akses Sekretaris atau Admin --}}
                        @if (Auth::user()->hak_akses->nama_hak_akses === 'Sekretaris' || Auth::user()->hak_akses->nama_hak_akses === 'Admin')
                            <th>Komentar</th>
                            <th><i class="fas fa-cog"></i></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->jenis_file }}</td>
                            <td>{{ $item->nama_file ?? '-' }}</td>
                            <td class="text-center">
                                @if ($item->path_file)
                                    <a href="{{ asset('storage/' . $item->path_file) }}" target="_blank">
                                        <i class="fas fa-file-download text-info mr-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum ada file</span>
                                @endif
                            </td>
                            {{-- Hanya tampil untuk hak_akses Sekretaris atau Admin --}}
                            @if (Auth::user()->hak_akses->nama_hak_akses === 'Sekretaris' || Auth::user()->hak_akses->nama_hak_akses === 'Admin')
                                <td>{{ $item->komentar ?? '-' }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hak_akses->nama_hak_akses === 'Sekretaris')
                                        @if ($item->status === 'disetujui')
                                            <span>-</span>
                                        @else
                                            <button class="btn btn-sm btn-outline-info align-self-start" 
                                                    data-toggle="modal" 
                                                    data-target="#modalKomentar{{ $item->id }}">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                        @endif
                                    @endif

                                    @php
                                        $masihAdaKomentar = \App\Models\Laporan::where('pergerakan_id', $laporanUtama->pergerakan_id)
                                            ->whereNotNull('komentar')
                                            ->where('komentar', '!=', '')
                                            ->exists();
                                    @endphp

                                    @if (Auth::user()->hak_akses->nama_hak_akses === 'Admin')
                                        @if ($item->komentar && in_array($item->status, ['draft', 'ditolak']))
                                            <!-- Tombol trigger modal -->
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-toggle="modal" 
                                                    data-target="#modalVerifikasi{{ $item->id }}"
                                                    title="Verifikasi komentar">
                                                <i class="fas fa-check"></i>
                                            </button>

                                            <!-- Modal Konfirmasi -->
                                            <div class="modal fade" id="modalVerifikasi{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalVerifikasiLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="modalVerifikasiLabel{{ $item->id }}">
                                                                Verifikasi Komentar
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Apakah Anda yakin bahwa komentar pada laporan ini <b>sudah direvisi</b> ?
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                                                <i class="fas fa-times"></i> Batal
                                                            </button>
                                                            <form action="{{ route('laporanVerifikasiKomentar', $item->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-check"></i> Ya, Sudah Direvisi
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @include('laporan/modal', ['item' => $item])
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada file laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                @if (Auth::user()->hak_akses->nama_hak_akses === 'Admin' && $laporanUtama->status === 'draft')
                    {{-- Jika masih ada komentar, tampilkan modal peringatan --}}
                    @if ($masihAdaKomentar)
                        <button type="button"
                                class="btn btn-sm btn-outline-warning"
                                data-toggle="modal"
                                data-target="#modalKomentarTersisa{{ $laporanUtama->id }}">
                            <i class="fas fa-exclamation-triangle"></i> Kirim Laporan
                        </button>

                        <!-- Modal Peringatan -->
                        <div class="modal fade" id="modalKomentarTersisa{{ $laporanUtama->id }}" tabindex="-1" role="dialog" aria-labelledby="modalKomentarTersisaLabel{{ $laporanUtama->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="modalKomentarTersisaLabel{{ $laporanUtama->id }}">
                                            Tidak Dapat Mengirim Laporan
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Masih terdapat komentar yang <strong>belum direvisi</strong> pada laporan ini.
                                        </div>
                                        <p>Harap lakukan revisi terlebih dahulu sebelum mengirim laporan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- Jika tidak ada komentar, tampilkan modal input resi --}}
                    @else
                        @if ($statusPergerakan === 'pihak_ketiga')
                            <button type="button"
                                    class="btn btn-sm btn-outline-info"
                                    data-toggle="modal"
                                    data-target="#modalResi{{ $laporanUtama->id }}">
                                <i class="fas fa-paper-plane"></i> Kirim Laporan
                            </button>
                            {{-- Modal input No. Resi --}}
                            @include('laporan/modal', ['laporanUtama' => $laporanUtama])

                        @elseif(in_array($statusPergerakan, ['cmp', 'tugboat']))
                            {{-- Langsung kirim laporan --}}
                            <form action="{{ route('laporanUpdateStatus', $laporanUtama->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="dikirim">
                                <button type="submit" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-paper-plane"></i> Kirim Laporan
                                </button>
                            </form>
                        @endif
                    @endif
                @endif

                {{-- Tombol untuk Sekretaris --}}
                @php
                    // Cek apakah masih ada komentar di laporan lain dengan pergerakan_id yang sama
                    $masihAdaKomentar = \App\Models\Laporan::where('pergerakan_id', $laporanUtama->pergerakan_id)
                        ->whereNotNull('komentar')
                        ->exists();
                @endphp

                {{-- Tombol untuk Sekretaris --}}
                @if (Auth::user()->hak_akses->nama_hak_akses === 'Sekretaris' && $laporanUtama->status === 'dikirim')

                    {{-- Tombol Setujui --}}
                    @if ($masihAdaKomentar)
                        <!-- Jika masih ada komentar, tampilkan modal tidak bisa setujui -->
                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#modalTidakBisaSetujui">
                            <i class="fas fa-check"></i> Setujui
                        </button>
                    @else
                        <!-- Jika tidak ada komentar, bisa setujui -->
                        <form action="{{ route('laporanUpdateStatus', $laporanUtama->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="disetujui">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                    @endif

                    {{-- Tombol Tolak --}}
                    @if ($masihAdaKomentar)
                        <!-- Jika masih ada komentar, bisa ditolak -->
                        <form action="{{ route('laporanUpdateStatus', $laporanUtama->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </form>
                    @else
                        <!-- Jika tidak ada komentar, tampilkan modal tidak bisa tolak -->
                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modalTidakBisaTolak">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    @endif

                @endif

                {{-- Modal tidak bisa setujui --}}
                <div class="modal fade" id="modalTidakBisaSetujui" tabindex="-1" role="dialog" aria-labelledby="modalTidakBisaSetujuiLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title" id="modalTidakBisaSetujuiLabel">
                                    Konfirmasi Disetujui
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Laporan ini <strong>tidak bisa disetujui</strong> karena masih ada komentar pada laporan ini.
                                </div>
                                <p>Silakan <strong>tolak laporan</strong> agar Admin dapat memberikan revisi terlebih dahulu.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal tidak bisa tolak --}}
                <div class="modal fade" id="modalTidakBisaTolak" tabindex="-1" role="dialog" aria-labelledby="modalTidakBisaTolakLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title" id="modalTidakBisaTolakLabel">
                                    Konfirmasi Ditolak
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Laporan ini <strong>tidak bisa ditolak</strong> karena semua file ini sudah benar (tidak ada komentar).
                                </div>
                                <p>Silakan <strong>setujui laporan</strong> untuk melanjutkan proses.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Revisi (hanya muncul jika status disetujui) --}}
                @if (Auth::user()->hak_akses->nama_hak_akses === 'Sekretaris' && $laporanUtama->status === 'disetujui')
                    <button type="button" 
                            class="btn btn-sm btn-outline-warning"
                            data-toggle="modal"
                            data-target="#modalRevisi{{ $laporanUtama->pergerakan_id }}">
                        <i class="fas fa-undo"></i> Revisi
                    </button>

                    <!-- Modal Konfirmasi Revisi -->
                    <div class="modal fade" id="modalRevisi{{ $laporanUtama->pergerakan_id }}" tabindex="-1" role="dialog" aria-labelledby="modalRevisiLabel{{ $laporanUtama->pergerakan_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="modalRevisiLabel{{ $laporanUtama->pergerakan_id }}">Konfirmasi Revisi Laporan</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form action="{{ route('laporanRevisi', $laporanUtama->pergerakan_id) }}" method="POST">
                                    @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Laporan ini <strong>sudah disetujui</strong>.<br>
                                                Melakukan revisi akan mengubah status laporan menjadi <strong>Draft</strong>.
                                            </div>

                                            <p>Apakah Anda yakin ingin melakukan revisi laporan ini? Jika iya, berikan alasan revisi</p>

                                            <div class="form-group">
                                                <label>Alasan direvisi:</label>
                                                <textarea name="alasan" class="form-control" rows="4" required></textarea>
                                            </div>   
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                            <form action="{{ route('laporanRevisi', $laporanUtama->pergerakan_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-undo"></i> Ya, direvisi
                                                </button>
                                            </form>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection