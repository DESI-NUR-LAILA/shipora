@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('port') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('portStore') }}" method="POST">
                @csrf

                <!-- Lokasi -->
                <div class="row mb-2">
                    <div class="col-xl-12">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Lokasi Port :
                        </label>
                        <input 
                            type="text" 
                            name="port" 
                            class="form-control @error('port') is-invalid @enderror" 
                            value="{{ old('port') }}" 
                            placeholder="Masukkan lokasi port">
                        @error('port')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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