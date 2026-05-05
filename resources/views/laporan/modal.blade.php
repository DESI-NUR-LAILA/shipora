<!-- Modal Komentar Sekretaris -->
<div class="modal fade" id="modalKomentar{{ $item->id }}" tabindex="-1" aria-labelledby="modalKomentarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKomentarLabel">Tambah Komentar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('laporanKomentar', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="komentar">Komentar</label>
                        <textarea name="komentar" id="komentar" class="form-control" rows="3" placeholder="Tulis komentar di sini...">{{ old('komentar', $item->komentar ?? '') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>
            </form>  
        </div>
    </div>
</div>

<!-- Modal Kirim Laporan -->
@isset($laporanUtama)
    <div class="modal fade" id="modalResi{{ $laporanUtama->id }}" tabindex="-1" role="dialog" aria-labelledby="modalResiLabel{{ $laporanUtama->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('laporanUpdateStatus', $laporanUtama->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="dikirim">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalResiLabel{{ $laporanUtama->id }}">Masukkan No. Resi</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No. Resi</label>
                            <input type="text" name="no_resi" class="form-control" placeholder="Contoh: JNT1234567890" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-paper-plane"></i> Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endisset