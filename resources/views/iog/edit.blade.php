@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-warning">
            <a href="{{ route('iog') }}" class="btn btn-sm btn-light text-warning">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('iogUpdate', $iog->id) }}" method="post">
                @csrf
                <div class="row mb-2">
                    <!-- Nomor Surat -->
                    <div class="col-xl-6">
                        <label class="form-label">Nomor Surat :</label>
                        <input 
                            type="text" 
                            name="nomor_surat" 
                            id="nomor_surat" 
                            class="form-control" 
                            value="{{ old('nomor_surat', $iog->nomor_surat) }}" 
                            readonly
                        >
                    </div>

                    <!-- Lampiran -->
                    <div class="col-xl-6">
                        <label class="form-label">Lampiran :</label>
                        <input 
                            type="number"
                            min="0"
                            name="lampiran" 
                            class="form-control @error('lampiran') is-invalid @enderror" 
                            value="{{ old('lampiran', $iog->lampiran) }}" 
                            placeholder="Masukkan lampiran">
                        @error('lampiran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Pegawai -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span> Pegawai :
                        </label>
                        <select name="pegawai_id" class="form-control @error('pegawai_id') is-invalid @enderror">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $item)
                                <option value="{{ $item->id }}" 
                                    {{ old('pegawai_id', $iog->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->id_card }} - {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Nama Kapal -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span> Nama Kapal :
                        </label>
                        <input 
                            type="text" 
                            name="nama_kapal" 
                            class="form-control @error('nama_kapal') is-invalid @enderror" 
                            value="{{ old('nama_kapal', $iog->nama_kapal) }}" 
                            placeholder="Masukkan nama kapal">
                        @error('nama_kapal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Master -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Master :
                        </label>
                        <input 
                            type="text" 
                            name="master" 
                            class="form-control @error('master') is-invalid @enderror" 
                            value="{{ old('master', $iog->master) }}" 
                            placeholder="Masukkan nama master">
                        @error('master')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Bendera -->
                    <div class="col-xl-6">
                        <label class="form-label">Bendera:</label>
                        <input 
                            type="text" 
                            name="bendera" 
                            class="form-control @error('bendera') is-invalid @enderror" 
                            value="{{ old('bendera', $iog->bendera) }}" 
                            placeholder="Masukkan bendera">
                        @error('bendera')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- GRT -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> GRT :
                        </label>
                        <input 
                            type="text" 
                            name="grt" 
                            class="form-control @error('grt') is-invalid @enderror" 
                            value="{{ old('grt', $iog->grt) }}" 
                            placeholder="Masukkan GRT">
                        @error('grt')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Pemilik -->
                    <div class="col-xl-6">
                        <label class="form-label">Pemilik:</label>
                        <input 
                            type="text" 
                            name="pemilik" 
                            class="form-control @error('pemilik') is-invalid @enderror" 
                            value="{{ old('pemilik', $iog->pemilik) }}" 
                            placeholder="Masukkan pemilik kapal">
                        @error('pemilik')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Tanggal Mulai -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span> Tanggal Mulai :
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                            value="{{ old('tanggal_mulai', $iog->tanggal_mulai) }}">
                        @error('tanggal_mulai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span> Tanggal Selesai :
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_selesai" 
                            class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                            value="{{ old('tanggal_selesai', $iog->tanggal_selesai) }}">
                        @error('tanggal_selesai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div>
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit mr-2"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection