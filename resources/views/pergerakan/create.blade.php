@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <a href="{{ route('pergerakan') }}" class="btn btn-sm btn-light text-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('pergerakanStore') }}" method="POST">
                @csrf
                <!-- Nama Kapal -->
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Nama Kapal :
                        </label>
                        <input 
                            type="text" 
                            name="ship_name" 
                            class="form-control @error('ship_name') is-invalid @enderror" 
                            value="{{ old('ship_name') }}" 
                            placeholder="Masukkan nama kapal">
                        @error('ship_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- GRT -->
                    <div class="col-xl-3">
                        <label class="form-label">
                            <span class="text-danger">*</span> GRT :
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="grt" 
                            class="form-control @error('grt') is-invalid @enderror" 
                            value="{{ old('grt') }}" 
                            placeholder="Masukkan GRT">
                        @error('grt')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- DWT -->
                    <div class="col-xl-3">
                        <label class="form-label">DWT :</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="dwt" 
                            class="form-control @error('dwt') is-invalid @enderror" 
                            value="{{ old('dwt') }}" 
                            placeholder="Masukkan DWT">
                        @error('dwt')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Flag dan Principal -->
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Bendera (Flag) :
                        </label>
                        <input 
                            type="text" 
                            name="flag" 
                            class="form-control @error('flag') is-invalid @enderror" 
                            value="{{ old('flag') }}" 
                            placeholder="Masukkan bendera">
                        @error('flag')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Principal :
                        </label>
                        <input 
                            type="text" 
                            name="principal" 
                            class="form-control @error('principal') is-invalid @enderror" 
                            value="{{ old('principal') }}" 
                            placeholder="Masukkan nama principal">
                        @error('principal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- ATA dan Last Port -->
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> ATA (Arrival Date):
                        </label>
                        <input 
                            type="date" 
                            name="ata" 
                            class="form-control @error('ata') is-invalid @enderror" 
                            value="{{ old('ata') }}">
                        @error('ata')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Last Port :
                        </label>
                        <input 
                            type="text" 
                            name="last_port" 
                            class="form-control @error('last_port') is-invalid @enderror" 
                            value="{{ old('last_port') }}" 
                            placeholder="Masukkan pelabuhan terakhir">
                        @error('last_port')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- ATD dan Next Port -->
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <label class="form-label">ATD (Departure Date):</label>
                        <input 
                            type="date" 
                            name="atd" 
                            class="form-control @error('atd') is-invalid @enderror" 
                            value="{{ old('atd') }}">
                        @error('atd')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label">Next Port :</label>
                        <input 
                            type="text" 
                            name="next_port" 
                            class="form-control @error('next_port') is-invalid @enderror" 
                            value="{{ old('next_port') }}" 
                            placeholder="Masukkan pelabuhan berikutnya">
                        @error('next_port')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <!-- Activities (Dropdown) -->
                    <div class="col-xl-6">
                        <label class="form-label">Activities :</label>
                        <select name="activities" class="form-control @error('activities') is-invalid @enderror">
                            <option value="" disabled selected>-- Pilih Activites --</option>
                            <option value="Discharge" {{ old('activities') == 'Discharge' ? 'selected' : '' }}>Discharge</option>
                            <option value="Loading" {{ old('activities') == 'Loading' ? 'selected' : '' }}>Loading</option>
                            <option value="Bunker" {{ old('activities') == 'Bunker' ? 'selected' : '' }}>Bunker</option>
                        </select>
                        @error('activities')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Jetty (Dropdown) -->
                    <div class="col-xl-6">
                        <label class="form-label">Jetty :</label>
                        <select name="jetty" class="form-control @error('jetty') is-invalid @enderror">
                            <option value="" disabled selected>-- Pilih Jetty --</option>
                            <option value="Pertamina" {{ old('jetty') == 'Pertamina' ? 'selected' : '' }}>Pertamina</option>
                            <option value="Pelindo" {{ old('jetty') == 'Pelindo' ? 'selected' : '' }}>Pelindo</option>
                        </select>
                        @error('jetty')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Status dan Cargo -->
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span> Status :
                        </label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="" {{ old('status') == '' ? 'selected' : '' }}>-- Pilih Status Kapal --</option>
                            <option value="CMP" {{ old('status') == 'CMP' ? 'selected' : '' }}>CMP</option>
                            <option value="Pihak Ketiga" {{ old('status') == 'Pihak Ketiga' ? 'selected' : '' }}>Pihak Ketiga</option>
                            <option value="Tugboat" {{ old('status') == 'Tugboat' ? 'selected' : '' }}>Tugboat</option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label">Cargo :</label>
                        <input 
                            type="text" 
                            name="cargo" 
                            class="form-control @error('cargo') is-invalid @enderror" 
                            value="{{ old('cargo') }}" 
                            placeholder="Masukkan jenis muatan">
                        @error('cargo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror  
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection