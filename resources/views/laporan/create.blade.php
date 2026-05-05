@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('laporan') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('laporanStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pergerakan_id" value="{{ request('pergerakan_id') }}">

                {{-- Pilih Kapal --}}
                <div class="form-group">
                    <label>Nama Kapal</label>
                    @if (request('pergerakan_id'))
                        {{-- Jika datang dari halaman detail --}}
                        @php
                            $selectedPergerakan = $pergerakan->firstWhere('id', request('pergerakan_id'));
                        @endphp
                        <input type="text" class="form-control" value="{{ $selectedPergerakan->ship_name }}" readonly>
                        <input type="hidden" name="pergerakan_id" id="pergerakan_id" value="{{ $selectedPergerakan->id }}">
                    @else
                        {{-- Jika akses langsung dari menu tambah laporan --}}
                        <select name="pergerakan_id" id="pergerakan_id" class="form-control" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach ($pergerakan as $item)
                                <option value="{{ $item->id }}" data-type="{{ strtolower($item->status) }}">
                                    {{ $item->ship_name }} (ATA: {{ \Carbon\Carbon::parse($item->ata)->format('d/m/Y') }}) (ATD: {{ $item->atd ? \Carbon\Carbon::parse($item->atd)->format('d-m-Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Container untuk Jenis File --}}
                <div id="jenis-file-container" style="display:none;">
                    <div id="file-upload-list"></div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript Dinamis --}}
    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kapalSelect = document.getElementById('pergerakan_id');

            // Kalau dari halaman detail (input hidden, bukan select)
            @if (request('pergerakan_id'))
                loadJenisFile("{{ $selectedPergerakan->id }}");
            @else
                // Kalau dari halaman tambah laporan (ada dropdown)
                kapalSelect.addEventListener('change', function() {
                    loadJenisFile(this.value);
                });
            @endif

            // Fungsi loadJenisFile tetap sama
            function loadJenisFile(kapalId) {
                const fileContainer = document.getElementById('jenis-file-container');
                const fileList = document.getElementById('file-upload-list');

                fileList.innerHTML = '';

                if (!kapalId) {
                    fileContainer.style.display = 'none';
                    return;
                }

                fetch(`/laporan/jenis-file/${kapalId}`)
                    .then(response => response.json())
                    .then(data => {
                        fileContainer.style.display = 'block';

                        if (data.available.length === 0 && data.uploaded.length > 0) {
                            fileList.innerHTML = '<p class="text-success">Semua file untuk kapal ini sudah diupload.</p>';
                            return;
                        }

                        if (data.uploaded.length > 0) {
                            const uploadedSection = document.createElement('div');
                            uploadedSection.innerHTML = `<h6 class="text-secondary mb-2">Sudah diupload:</h6>`;
                            data.uploaded.forEach(jenis => {
                                const badge = document.createElement('span');
                                badge.classList.add('badge', 'badge-success', 'mr-2', 'mb-2');
                                badge.textContent = jenis;
                                uploadedSection.appendChild(badge);
                            });
                            fileList.appendChild(uploadedSection);
                        }

                        if (data.available.length > 0) {
                            const availableSection = document.createElement('div');
                            availableSection.innerHTML = `<h6 class="text-primary mt-3 mb-2">Upload File:</h6>`;
                            data.available.forEach(jenis => {
                                const group = document.createElement('div');
                                group.classList.add('form-group', 'border', 'p-3', 'mb-3', 'rounded');
                                group.innerHTML = `
                                    <label><strong>${jenis}</strong></label>
                                    <input type="hidden" name="jenis_file[]" value="${jenis}">
                                    <input type="file" name="path_file[]" class="form-control-file file-input" data-jenis="${jenis}">
                                `;
                                availableSection.appendChild(group);
                            });
                            fileList.appendChild(availableSection);

                            document.querySelectorAll('.file-input').forEach(input => {
                                input.addEventListener('change', function() {
                                    const parent = this.closest('.form-group');
                                    const label = parent.querySelector('label strong');
                                    if (this.files.length > 0) {
                                        parent.classList.add('border-success');
                                        parent.classList.remove('border-danger');
                                        label.textContent = `${this.dataset.jenis} ✅`;
                                    } else {
                                        parent.classList.remove('border-success');
                                        parent.classList.add('border-danger');
                                        label.textContent = this.dataset.jenis;
                                    }
                                });
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
        </script>
    @endpush
@endsection