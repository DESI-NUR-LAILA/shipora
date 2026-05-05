@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-archive mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-secondary">
            <a href="{{ route('ttd') }}" class="btn btn-sm btn-light text-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-secondary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Lokasi Port</th>
                        <th>Hak Akses</th>
                        <th>Tanda Tangan</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ttd as $item)
                        <tr class="align-middle">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->port?->port ?? '-' }}</td>

                            @php
                                $badgeMap = [
                                    'Admin' => ['class' => 'dark', 'icon' => 'fas fa-user-tie'],
                                    'PIC' => ['class' => 'info', 'icon' => 'fas fa-user-shield'],
                                    'Sekretaris' => ['class' => 'primary', 'icon' => 'fas fa-user-cog'],
                                    'HOA' => ['class' => 'success', 'icon' => 'fas fa-user-check'],
                                    'Supervisor' => ['class' => 'warning', 'icon' => 'fas fa-user-tag'],
                                ];
                                $defaultBadge = ['class' => 'light', 'icon' => 'fas fa-user'];
                                $hak_akses = $item->hak_akses?->nama_hak_akses;
                                $badge = $badgeMap[$hak_akses] ?? $defaultBadge;
                            @endphp

                            <td class="text-center">
                                <span class="badge badge-{{ $badge['class'] }} px-3 py-2 shadow-sm">
                                    <i class="{{ $badge['icon'] }} mr-1"></i>
                                    {{ $hak_akses ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if ($item->ttd_path)
                                    <img src="{{ asset('storage/' . $item->ttd_path) }}" alt="TTD" height="40">
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success shadow-sm"
                                        data-toggle="modal"
                                        data-target="#unarsipModal{{ $item->id }}"
                                        title="Aktifkan kembali">
                                    <i class="fas fa-undo"></i>
                                </button>

                                {{-- Modal Unarsip --}}
                                <div class="modal fade" id="unarsipModal{{ $item->id }}" tabindex="-1" aria-labelledby="unarsipModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Konfirmasi Aktifkan Data</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p>Apakah Anda yakin ingin mengaktifkan data ini kembali?</p>
                                                <div class="border rounded p-3 bg-light mt-2">
                                                    <p class="mb-1"><strong>Nama:</strong> {{ $item->nama }}</p>
                                                    <p class="mb-1"><strong>Lokasi Port:</strong> {{ $item->port?->port ?? '-' }}</p>
                                                    <p class="mb-0"><strong>Hak Akses:</strong> {{ $item->hak_akses?->nama_hak_akses ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                                                    <i class="fas fa-times"></i> Tutup
                                                </button>
                                                <form action="{{ route('ttdUnarsip', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo"></i> Ya, Aktifkan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data arsip</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection