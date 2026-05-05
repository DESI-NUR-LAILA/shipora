@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('pegawai') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('pegawaiStore') }}" method="post" enctype="multipart/form-data" id="formPegawai">
                @csrf
                <input type="hidden" name="signature" id="signatureInput">

                <div class="row mb-2">
                    <!-- ID Card -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            ID Card :
                        </label>
                        <input type="text" name="id_card" class="form-control @error('id_card') is-invalid @enderror" 
                               value="{{ old('id_card') }}" placeholder="Masukkan ID Card">
                        @error('id_card')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Nama :
                        </label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                               value="{{ old('nama') }}" placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Penempatan -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            Penempatan :
                        </label>
                        <input type="text" class="form-control" 
                            value="{{ $userPort->port ?? '-' }}" readonly>
                    </div>

                    <!-- Asal -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Asal :
                        </label>
                        <input type="text" name="asal" class="form-control @error('asal') is-invalid @enderror" 
                            value="{{ old('asal') }}" placeholder="Masukkan asal">
                        @error('asal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Canvas Tanda Tangan --}}
                <div class="col-xl-6 mx-auto">
                    <label class="form-label d-block text-center mb-2">
                        <span class="text-danger">*</span> Tanda Tangan :
                    </label>

                    <canvas id="pegawaiCanvas" width="400" height="200"
                        class="d-block mx-auto @error('signature') border border-danger @enderror"
                        style="border: 2px dashed #999; border-radius: 8px; background-color: #fff;">
                    </canvas>

                    @error('signature')
                        <div class="text-danger text-center mt-2">{{ $message }}</div>
                    @enderror

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-secondary mr-2" id="clearPegawai">
                            <i class="fas fa-eraser mr-1"></i> Bersihkan
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    const canvas = document.getElementById('pegawaiCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        const getPos = e => {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        };

        const startDraw = e => { drawing = true; ctx.beginPath(); ctx.moveTo(...Object.values(getPos(e))); };
        const endDraw = () => { drawing = false; };
        const draw = e => { if (!drawing) return; const pos = getPos(e); ctx.lineTo(pos.x, pos.y); ctx.stroke(); };

        ['mousedown','touchstart'].forEach(ev => canvas.addEventListener(ev, startDraw));
        ['mouseup','mouseleave','touchend'].forEach(ev => canvas.addEventListener(ev, endDraw));
        ['mousemove','touchmove'].forEach(ev => canvas.addEventListener(ev, e => { e.preventDefault(); draw(e); }));

        document.getElementById('clearPegawai').addEventListener('click', () => ctx.clearRect(0,0,canvas.width,canvas.height));

        document.getElementById('formPegawai').addEventListener('submit', e => {
            const imageData = ctx.getImageData(0,0,canvas.width,canvas.height).data;
            const empty = Array.from(imageData).every(v => v === 0);
            document.getElementById('signatureInput').value = empty ? '' : canvas.toDataURL('image/png');
        });
    }
    </script>
@endpush