@php
    switch($aksi) {
        case 'Mengetahui':
            $headerClass = 'bg-primary text-white';
            $btnClass = 'btn-outline-primary';
            $btnIcon = 'fas fa-check';
            $route = route('cutiMengetahui', $item->id);
            break;

        case 'Menyetujui':
            $headerClass = 'bg-success text-white';
            $btnClass = 'btn-outline-success';
            $btnIcon = 'fas fa-check';
            $route = route('cutiApprove', $item->id);
            break;

        case 'Menolak':
            $headerClass = 'bg-danger text-white';
            $btnClass = 'btn-outline-danger';
            $btnIcon = 'fas fa-times';
            $route = route('cutiTolak', $item->id);
            break;

        default:
            $headerClass = 'bg-secondary text-white';
            $btnClass = 'btn-outline-secondary';
            $btnIcon = 'fas fa-question';
            $route = '#';
            break;
    }
@endphp

<!-- Modal -->
<div class="modal fade" id="modal{{ $aksi }}{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title">
                    {{ $aksi }} Cuti
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>

            <form action="{{ $route }}" method="POST">
                @csrf

                <!-- Body -->
                <div class="modal-body text-left">
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Nama</div>
                        <div class="col-7">: {{ $item->pegawai->nama ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Tanggal Mulai</div>
                        <div class="col-7">: {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Tanggal Selesai</div>
                        <div class="col-7">: {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Sisa Hak Cuti</div>
                        <div class="col-7">: {{ $item->sisa_hak_cuti ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Keperluan</div>
                        <div class="col-7">: {{ $item->keterangan ?? '-' }}</div>
                    </div>

                    {{-- Tampilkan field alasan kalau aksi = Menolak --}}
                    @if($aksi === 'Menolak')
                    @method('PUT')
                        <div class="form-group">
                            <label for="alasan_penolakan" class="font-weight-bold">Alasan Penolakan :</label>
                            <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-sm {{ $btnClass }}">
                        <i class="{{ $btnIcon }}"></i> {{ $aksi }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalEditLabel{{ $item->id }}">
                    Konfirmasi Edit Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Mengedit data ini status akan menjadi <strong>Pending</strong>
                </div>
                <p>Apakah Anda yakin ingin melanjutkan proses edit data cuti ini?</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <a href="{{ route('cutiEdit', $item->id) }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit"></i> Lanjutkan Edit
                </a>
            </div>
        </div>
    </div>
</div>