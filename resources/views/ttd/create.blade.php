@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus mr-2"></i>
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header bg-primary">
        <a href="{{ route('ttd') }}" class="btn btn-sm btn-light text-primary">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('ttdStore') }}" method="POST" enctype="multipart/form-data" id="formTtd">
            @csrf

            <input type="hidden" name="signature" id="signatureInput">

            {{-- Input Nama --}}
            <div class="row mb-2">
                <div class="col-xl-12">
                    <label class="form-label"><span class="text-danger">*</span> Nama :</label>
                    <input type="text" name="nama" 
                        class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama') }}" 
                        placeholder="Masukkan nama">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pilih Port --}}
            <div class="row mb-2">
                <!-- Lokasi Port -->
                <div class="col-xl-6 mb-2">
                    <label class="form-label">
                        <span class="text-danger">*</span>
                        Lokasi Port :
                    </label>
                    <select name="port_id" class="form-control @error('port_id') is-invalid @enderror">
                        <option value="" selected disabled>-- Pilih Lokasi Port --</option>
                        @foreach($ports as $item)
                            <option value="{{ $item->id }}" {{ old('port_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->port }}
                            </option>
                        @endforeach
                    </select>
                    @error('port_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Hak Akses -->
                <div class="col-xl-6">
                    <label class="form-label">
                        <span class="text-danger">*</span>
                        Hak Akses :
                    </label>
                    <select name="hak_akses_id" class="form-control @error('hak_akses_id') is-invalid @enderror">
                        <option value="" selected disabled>-- Pilih Hak Akses --</option>
                        @foreach($hak_akses as $item)
                            <option value="{{ $item->id }}" {{ old('hak_akses_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_hak_akses }}
                            </option>
                        @endforeach
                    </select>
                    @error('hak_akses_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Canvas Tanda Tangan --}}
            <div class="col-xl-6 mx-auto">
                <label class="form-label d-block text-center mb-2">
                    <span class="text-danger">*</span> Tanda Tangan :
                </label>

                <canvas id="ttdCanvas" width="400" height="200"
                    class="d-block mx-auto @error('signature') border border-danger @enderror"
                    style="border: 2px dashed #999; border-radius: 8px; background-color: #fff;">
                </canvas>

                @error('signature')
                    <div class="text-danger text-center mt-2">{{ $message }}</div>
                @enderror

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-sm btn-secondary mr-2" id="clearTtd">
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
    const canvas = document.getElementById('ttdCanvas');
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

        document.getElementById('clearTtd').addEventListener('click', () => ctx.clearRect(0,0,canvas.width,canvas.height));

        document.getElementById('formTtd').addEventListener('submit', e => {
            const imageData = ctx.getImageData(0,0,canvas.width,canvas.height).data;
            const empty = Array.from(imageData).every(v => v === 0);
            document.getElementById('signatureInput').value = empty ? '' : canvas.toDataURL('image/png');
        });
    }
    </script>
@endpush