@extends('layouts.app')

@section('title', 'Detail Pengukuran')

@section('content')


<div class="container-fluid mt-2">
    <a href="{{ auth()->user()->role === 'admin' ? route('dashboard.admin') : route('dashboard.user') }}" class="mb-1 py-0 btn btn-sm btn-outline-warning rounded-pill">
    <i class="bi bi-arrow-left"></i> BACK
    </a>
<div class="row g-3 align-items-stretch">
    <!-- Kolom 1: Icon / Foto Truck Isometrik -->
    <div class="col-md-2 d-flex h-100">
        <div class="truck-icon w-100 d-flex flex-column justify-content-center p-2">
            <i class="bi bi-truck-front-fill text-warning" style="font-size: 5rem;"></i>
            @php
                $extra = [];
                if ($measurement->lidarRawData && $measurement->lidarRawData->raw_data) {
                    $raw = $measurement->lidarRawData->raw_data;
                    if (is_string($raw)) {
                        $decoded = json_decode($raw, true);
                        if (is_array($decoded)) $extra = $decoded;
                    } else {
                        $extra = $raw;
                    }
                }
            @endphp

            <p class="text-warning mt-2 mb-0">PLAT NOMER{{ $extra['truck'] ?? $measurement->notes ?? 'B 1234 XYZ' }}</p>
            <div class="small text-white-50">Model: {{ $extra['model'] ?? 'Hino FM 260' }}</div>
            <div class="small text-white-50">{{ $extra['driver'] ?? $measurement->user->name ?? 'Bambang' }}</div>
            <div>

            </div>
        </div>
    </div>

    <!-- Kolom 2: Detail Pengukuran -->
    <div class="col-md-4 d-flex h-100">
        <div class="info-card w-100">
            @php
                $extra = [];
                if ($measurement->lidarRawData && $measurement->lidarRawData->raw_data) {
                    $raw = $measurement->lidarRawData->raw_data;
                    if (is_string($raw)) {
                        $decoded = json_decode($raw, true);
                        if (is_array($decoded)) $extra = $decoded;
                    } else {
                        $extra = $raw;
                    }
                }
            @endphp

            <div class="row mb-0">
                <div class="col-md-6 mb-0 py-0">
                    <div class="info-label">Nomor Plat</div>
                    <div class="info-value bg-white px-2 border-1 text-dark w-100">{{ $extra['truck'] ?? $measurement->notes ?? 'B 1234 XYZ' }}</div>
                </div>
                <div class="col-md-6 mb-0 py-0">
                    <div class="info-label">Nama Pengemudi</div>
                    <div class="info-value">{{ $extra['driver'] ?? $measurement->user->name ?? 'Bambang' }}</div>
                </div>
            </div>
            <hr class="bg-warning mb-0 mt-0">

            <div class="row mb-0">
                <div class="col-md-6 mb-0 py-0">
                    <div class="info-label mb-0">Ukuran Bak (P x L x T)</div>
                    <div class="info-value mb-0">
                        {{ $extra['length'] ?? $measurement->length_m ?? 4.2 }} m × 
                        {{ $extra['width'] ?? $measurement->width_m ?? 2.1 }} m × 
                        {{ $extra['height'] ?? $measurement->height_avg_m ?? 1.5 }} m
                    </div>
                </div>
                <div class="col-md-6 mb-0 py-0">
                    <div class="info-label">Waktu Ukur</div>
                    <div class="info-value">{{ $measurement->measured_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
            <hr class="bg-warning mb-0 mt-0">

            <div class="row mb-0">
                <div class="col-md-4 mb-0 py-0">
                    <div class="info-label">Material</div>
                    <div class="info-value">{{ $extra['material'] ?? 'Pasir Urug' }}</div>
                </div>
                <div class="col-md-4 mb-0 py-0">
                    <div class="info-label">Berat (est.)</div>
                    <div class="info-value">{{ number_format(($extra['cubic_meters'] ?? $measurement->volume_m3) * 1.6, 2) }} ton</div>
                </div>
                <div class="col-md-4 mb-0 py-0">
                    <div class="info-label mb-0">Volume (m³)</div>
                    <div class="info-value mb-0" style="font-size: 1.5rem; color:#ffd700;">{{ number_format($measurement->volume_m3, 2) }} m³</div>
                </div>
            </div>
            <hr class="bg-warning mb-0 mt-0">

            <div class="row mb-0">
                <div class="col-12">
                    <div class="info-label mb-0">Keterangan</div>
                    <div class="fst-italy text-muted bg-white" >
                        Menggunakan:  {{ $measurement->method }} – 
                        @if(isset($extra['cubic_meters']))
                            Target: {{ $extra['cubic_meters'] }} m³
                        @else
                            point cloud terekam.
                        @endif
                    </div>
                </div>
            </div>
    </div>
    </div>
</div>

    <!-- Card Point Cloud Hasil LiDAR -->
    <div class="card card-voltruck mt-0">
        <div class="card-header">
            🧾 Visualisasi Point Cloud (Hasil Kamera LiDAR)
        </div>
        <div class="card-body">
            <canvas id="pointCloudCanvas" width="800" height="400" style="width:100%; height:auto; max-width:800px; display:block; margin:0 auto;"></canvas>
            <p class="text-center text-white-50 mt-2">Titik-titik hasil pemindaian LiDAR membentuk bentuk bak truk dan muatan.</p>
        </div>
    </div>
</div>

<script>
    // Data point cloud dari server (dalam bentuk array of {x,y,z})
    const pointCloud = @json($pointCloud);
    
    // Konfigurasi canvas
    const canvas = document.getElementById('pointCloudCanvas');
    const ctx = canvas.getContext('2d');
    
    // Atur ukuran canvas agar responsif (tapi kita set lebar 800, tinggi 400)
    canvas.width = 800;
    canvas.height = 400;
    
    // Cari batas x, y untuk scaling
    let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
    pointCloud.forEach(p => {
        if (p.x < minX) minX = p.x;
        if (p.x > maxX) maxX = p.x;
        if (p.y < minY) minY = p.y;
        if (p.y > maxY) maxY = p.y;
    });
    // Tambahkan padding
    const padding = 0.2;
    const rangeX = (maxX - minX) + 2*padding;
    const rangeY = (maxY - minY) + 2*padding;
    const scaleX = canvas.width / rangeX;
    const scaleY = canvas.height / rangeY;
    
    function mapX(x) {
        return ((x - (minX - padding)) * scaleX);
    }
    function mapY(y) {
        // Karena canvas Y ke bawah, balik
        return canvas.height - ((y - (minY - padding)) * scaleY);
    }
    
    // Gambar background
    ctx.fillStyle = '#1a1a1a';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Gambar titik-titik
    for (let i = 0; i < pointCloud.length; i++) {
        const p = pointCloud[i];
        // Warna berdasarkan ketinggian z (merah = tinggi, biru = rendah)
        const intensity = Math.min(1, Math.max(0, (p.z + 0.2) / 1.5)); // asumsi z antara -0.2 sampai 1.3
        const r = 255 * intensity;
        const g = 200 * intensity;
        const b = 100 * intensity;
        ctx.fillStyle = `rgb(${r}, ${g}, ${b})`;
        
        const x = mapX(p.x);
        const y = mapY(p.y);
        ctx.beginPath();
        ctx.arc(x, y, 2, 0, 2 * Math.PI);
        ctx.fill();
    }
    
    // Gambar outline bak (opsional)
    ctx.strokeStyle = '#ffd700';
    ctx.lineWidth = 2;
    // Asumsikan batas bak adalah dari minX ke maxX, minY ke maxY
    ctx.strokeRect(mapX(minX), mapY(maxY), (maxX-minX)*scaleX, (maxY-minY)*scaleY);
</script>
@endsection