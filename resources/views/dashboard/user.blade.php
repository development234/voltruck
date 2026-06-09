@extends('layouts.app')

@section('title', 'Dashboard User - VolTruck')

@section('content')

<div class="row g-3 mb-4">
    <!-- Card 1: Info User -->
    <div class="col-md-4">
        <div class="card card-voltruck h-100">
            <div class="card-header">
                <i class="bi bi-person-circle"></i> 
                <span style="color:#ffd700;">{{ Auth::user()->name }}</span>
            </div>
            <div class="card-body">
                <p class="mb-1" style="color:#ffd700; font-weight:600;">📋 Fitur yang tersedia:</p>
                <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                    <li>Form input pengukuran volume truk</li>
                    <li>Riwayat pengukuran milik Anda</li>
                    <li>Grafik volume harian</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Card 2: Pengukuran Bulan Ini -->
    <div class="col-md-4">
        <div class="card card-voltruck h-100">
            <div class="card-header">📊 Bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</div>
            <div class="card-body text-center">
                @php
                    $bulanIni = $measurements->filter(function ($m) {
                        return $m->measured_at->month === now()->month && 
                               $m->measured_at->year === now()->year;
                    })->count();
                @endphp
                <h3 style="color:#ffd700;">{{ $bulanIni }}</h3>
                <p class="mb-0">Kali pengukuran</p>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Volume -->
    <div class="col-md-4">
        <div class="card card-voltruck h-100">
            <div class="card-header">📈 Total Volume</div>
            <div class="card-body text-center">
                @php
                    $totalVolume = $measurements->sum('volume_m3');
                @endphp
                <h3 style="color:#ffd700;">{{ number_format($totalVolume, 2) }} m³</h3>
                <p class="mb-0">Akumulasi muatan</p>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
        <!-- ... bagian kartu statistik sebelumnya ... -->
        <div class="card card-voltruck h-100">
            <div class="card-header">
                📋 Riwayat Pengukuran
            </div>
            <div class="card-body">
                @if($measurements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-voltruck table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Volume (m³)</th>
                                    <th>Metode</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($measurements as $index => $measurement)
                                <tr>
                                    <td>{{ $measurements->firstItem() + $index }}</td>
                                    <td>{{ $measurement->measured_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                            {{ number_format($measurement->volume_m3, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($measurement->method) }}</td>
                                    <td>{{ $measurement->notes ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('measurement.show', $measurement->id) }}" 
                                        class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $measurements->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="mt-2 mb-0">Belum ada pengukuran. Silakan lakukan pengukuran pertama.</p>
                    </div>
                @endif
            </div>
        </div>

</div>


                <div class="alert alert-voltruck mt-3 py-0">
                    <strong>🚛 Contoh komponen:</strong> Kamera atau LiDAR akan diintegrasikan di sini untuk pengukuran otomatis.
                </div>

                <div class="text-center mt-2">
                    <a href="{{ route('measurement.show') }}" class="btn btn-sm py-0 btn-voltruck">
                        📊 Lihat Detail Pengukuran Terbaru
                    </a>
                </div>

                <!--<div class="text-center mt-4">
                    <button class="btn btn-voltruck" onclick="alert('Fitur pengukuran akan segera hadir')">
                        🚀 Mulai Ukur Volume
                    </button>
                </div>-->
            </div>
        </div>

    </div>
</div>
@endsection