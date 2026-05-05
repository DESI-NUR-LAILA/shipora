@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-warning">
            <a href="{{ route('port') }}" class="btn btn-sm btn-light text-warning">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('portUpdate', $port->id) }}" method="post">
                @csrf
                <div class="row mb-2">
                    <div class="col-xl-12 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>    
                            Lokasi Port:
                        </label>
                        <input type="text" name="port" class="form-control @error('port') is-invalid @enderror" value="{{ $port->port }}">
                        @error('port')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <!-- Tombol Simpan -->
                    <div>
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit mr-2"></i>
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection