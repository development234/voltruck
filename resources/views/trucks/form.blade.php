@extends('layouts.app')

@section('title', isset($truck) ? 'Edit Truk' : 'Tambah Truk')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-warning">{{ isset($truck) ? '✏️ Edit Truk' : '➕ Tambah Truk Baru' }}</h6>
        <a href="{{ route('trucks.index') }}" class="btn-voltruck py-0">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card card-voltruck">
        <div class="card-header">📝 Form Data Truk</div>
        <div class="card-body">
            <form action="{{ isset($truck) ? route('trucks.update', $truck->id) : route('trucks.store') }}" method="POST">
                @csrf
                @if(isset($truck))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" 
                               value="{{ old('plate_number', $truck->plate_number ?? '') }}" required>
                        @error('plate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Nama Truk</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $truck->name ?? '') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Model</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                               value="{{ old('model', $truck->model ?? '') }}">
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Kapasitas (m³)</label>
                        <input type="number" step="0.01" name="capacity_m3" class="form-control @error('capacity_m3') is-invalid @enderror" 
                               value="{{ old('capacity_m3', $truck->capacity_m3 ?? '') }}">
                        @error('capacity_m3')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-warning">Panjang Bak (m) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="length_m" class="form-control @error('length_m') is-invalid @enderror" 
                               value="{{ old('length_m', $truck->length_m ?? '') }}" required>
                        @error('length_m')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-warning">Lebar Bak (m) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="width_m" class="form-control @error('width_m') is-invalid @enderror" 
                               value="{{ old('width_m', $truck->width_m ?? '') }}" required>
                        @error('width_m')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-warning">Tinggi Bak (m) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="height_m" class="form-control @error('height_m') is-invalid @enderror" 
                               value="{{ old('height_m', $truck->height_m ?? '') }}" required>
                        @error('height_m')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-voltruck py-0">
                        <i class="bi bi-save"></i> {{ isset($truck) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection