@extends('layouts.app')

@section('title', 'Belum Ada Pengukuran - VolTruck')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card login-card text-center p-5" style="max-width: 500px;">
        <div class="mb-4">
            <i class="bi bi-inbox fs-1 text-warning" style="font-size: 4rem;"></i>
        </div>
        <h3 class="text-warning mb-3">Belum Ada Data Pengukuran</h3>
        <p class="text-white-50 mb-4">
            Belum ada hasil pengukuran volume muatan truk. Silakan lakukan pengukuran pertama menggunakan simulator LiDAR atau form manual.
        </p>
        <div class="d-grid gap-2">
            <a href="{{ route('dashboard.user') }}" class="btn btn-voltruck">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button onclick="alert('Fitur pengukuran akan segera hadir. Silakan jalankan script Python simulator.')" class="btn btn-outline-warning">
                <i class="bi bi-play-fill"></i> Mulai Simulasi Pengukuran
            </button>
        </div>
        <div class="mt-4 text-white-50 small">
            <i class="bi bi-info-circle"></i> Pastikan Anda sudah mengirim data dari LiDAR simulator ke API endpoint.
        </div>
    </div>
</div>
@endsection