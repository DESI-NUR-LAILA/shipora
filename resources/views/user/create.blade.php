@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('user') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('userStore') }}" method="post">
                @csrf
                <div class="row mb-2">
                    <!-- Email -->
                     <div class="col-xl-12">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Email :
                        </label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" placeholder="Masukkan email aktif">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

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

                <div class="row mb-2">
                    <!-- Password -->
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Password :
                        </label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Konfirmasi Password :
                        </label>
                        <input type="password" name="password_confirmation" class="form-control" 
                               placeholder="Ulangi password">
                    </div>
                </div>

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