@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('cuti') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('cutiStore') }}" method="POST">
                @csrf
                <div class="row mb-2">
                    <!-- Pegawai -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Pegawai :
                        </label>
                        <select name="pegawai_id" class="form-control @error('pegawai_id') is-invalid @enderror">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $item)
                                <option value="{{ $item->id }}" {{ old('pegawai_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->id_card }} - {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Tujuan Cuti -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Tujuan Cuti :
                        </label>
                        <input 
                            type="text" 
                            name="tujuan" 
                            class="form-control @error('tujuan') is-invalid @enderror" 
                            value="{{ old('tujuan') }}" 
                            placeholder="Masukkan tujuan cuti">
                        @error('tujuan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Tanggal Mulai -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Tanggal Mulai :
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                            value="{{ old('tanggal_mulai') }}">
                        @error('tanggal_mulai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Tanggal Selesai :
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_selesai" 
                            class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                            value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Keterangan -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Keterangan / Keperluan :
                        </label>
                        <input 
                            type="text"
                            name="keterangan" 
                            class="form-control @error('keterangan') is-invalid @enderror" 
                            value="{{ old('keterangan') }}" 
                            placeholder="Misal: Urusan keluarga, pribadi, dll">
                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Berkendaraan -->
                    <div class="col-xl-3">
                        <label class="form-label">
                            Kendaraan :
                        </label>
                        <select name="berkendaraan" class="form-control @error('berkendaraan') is-invalid @enderror">
                            <option value="">-- Pilih Kendaraan --</option>
                            <option value="Pribadi" {{ old('berkendaraan') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                            <option value="Umum" {{ old('berkendaraan') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                        @error('berkendaraan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Hari Libur -->
                    <div class="col-xl-3">
                        <label class="form-label">Hari Libur :</label>

                        <input type="number"
                            name="hari_libur"
                            class="form-control @error('hari_libur') is-invalid @enderror"
                            value="{{ old('hari_libur', $cuti->hari_libur ?? 0) }}"
                            min="0"
                            step="1"
                            placeholder="Masukkan jumlah hari libur">

                        @error('hari_libur')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <small class="text-warning">
                            *Isi apabila dalam rentang cuti terdapat hari libur.
                        </small>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
