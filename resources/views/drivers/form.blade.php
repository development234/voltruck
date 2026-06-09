@extends('layouts.app')

@section('title', isset($driver) ? 'Edit Pengemudi' : 'Tambah Pengemudi')

@section('content')
<div class="container-fluid mt-1">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-warning">{{ isset($driver) ? '✏️ Edit Pengemudi' : 'DRVER' }}</h6>
        <a href="{{ route('drivers.index') }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card card-voltruck">
        <div class="card-header">📝 Form Data Pengemudi</div>
        <div class="card-body">
            <form action="{{ isset($driver) ? route('drivers.update', $driver->id) : route('drivers.store') }}" method="POST">
                @csrf
                @if(isset($driver))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label text-warning">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $driver->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Nomor Lisensi (SIM)</label>
                        <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" 
                               value="{{ old('license_number', $driver->license_number ?? '') }}">
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-warning">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $driver->phone ?? '') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn bt-sm btn-voltruck py-0 fw-bold">
                        <i class="bi bi-save"></i> {{ isset($driver) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection