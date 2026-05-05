@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-warning">
            <a href="{{ route('pegawai') }}" class="btn btn-sm btn-light text-warning">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('pegawaiUpdate', $pegawai->id) }}" method="post">
                @csrf
                <div class="row mb-2">
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>    
                            ID Card :
                        </label>
                        <input type="text" name="id_card" class="form-control @error('id_card') is-invalid @enderror" value="{{ $pegawai->id_card }}">
                        @error('id_card')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>    
                            Nama :
                        </label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ $pegawai->nama }}">
                        @error('nama')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span> Penempatan :
                        </label>

                        <!-- Tampilkan nama port (readonly) -->
                        <input type="text" 
                            class="form-control" 
                            value="{{ $pegawai->port->port ?? '-' }}" 
                            readonly>

                        <!-- Hidden input agar id tetap terkirim -->
                        <input type="hidden" 
                            name="port_id" 
                            value="{{ $pegawai->port_id }}">
                    </div>

                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>    
                            Asal :
                        </label>
                        <input type="text" name="asal" class="form-control @error('asal') is-invalid @enderror" value="{{ $pegawai->asal }}">
                        @error('asal')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
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