@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-warning">
            <a href="{{ route('laporan') }}" class="btn btn-sm btn-light text-warning">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('laporanUpdate', $laporan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Nama Kapal (readonly) --}}
                <div class="form-group">
                    <label>Nama Kapal</label>
                    <input type="text" class="form-control" value="{{ $laporan->pergerakan->ship_name }}" readonly>
                    <input type="hidden" name="pergerakan_id" id="pergerakan_id" value="{{ $laporan->pergerakan_id }}">
                </div>

                {{-- Container Jenis File --}}
                <div id="jenis-file-container" style="display:none;">
                    <h5 class="mt-4 mb-3">Upload Berdasarkan Jenis File</h5>
                    <div id="file-upload-list"></div>
                </div>

                {{-- No Resi --}}
                <div class="form-group mt-4" id="no_resi_section" style="display: none;">
                    <label>No Resi</label>
                    <input type="text" name="no_resi" class="form-control" placeholder="contoh: JNT1234567890">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="fas fa-save mr-2"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const laporan = @json($laporan);

        document.addEventListener('DOMContentLoaded', function () {
            const laporanId = {{ $laporan->id }};
            const container = document.getElementById('jenis-file-container');
            const fileList = document.getElementById('file-upload-list');

            function loadJenisFileEdit(laporanId) {
                fetch(`/laporan/edit-jenis-file/${laporanId}`)
                    .then(response => response.json())
                    .then(data => {
                        container.style.display = 'block';
                        fileList.innerHTML = '';

                        if (data.uploaded.length > 0) {
                            data.uploaded.forEach(item => {
                                const group = document.createElement('div');
                                group.classList.add('form-group', 'border', 'border-warning', 'p-3', 'mb-3', 'rounded');

                                group.innerHTML = `
                                    <label><strong>${item.jenis_file}</strong></label>
                                    <input type="hidden" name="jenis_file[]" value="${item.jenis_file}">

                                    ${item.path_file ? `
                                        <p class="mt-2">File lama:
                                            <a href="${item.path_file}" target="_blank" class="text-primary">
                                                ${item.nama_file || 'Lihat File'}
                                            </a>
                                        </p>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="hapus_file[]" value="${item.jenis_file}" id="hapus_${item.jenis_file}">
                                            <label class="form-check-label text-danger" for="hapus_${item.jenis_file}">
                                                Hapus file ini
                                            </label>
                                        </div>
                                    ` : ''}

                                    <input type="file" name="path_file[]" class="form-control-file mt-2">
                                    <small class="text-muted">*Kosongkan jika tidak ingin mengganti file</small>
                                `;

                                fileList.appendChild(group);
                            });
                        } else {
                            fileList.innerHTML = `<p class="text-muted">Belum ada file yang diupload untuk laporan ini.</p>`;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Auto-load saat halaman dibuka
            loadJenisFileEdit(laporanId);
        });
    </script>
    @endpush
@endsection