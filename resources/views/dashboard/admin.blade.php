@extends('layouts.app')

@section('title', 'Dashboard Admin - VolTruck')

@section('content')
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card card-voltruck">
                <div class="card-header"><i class="bi bi-people"></i> Total Operator</div>
                <div class="card-body text-center">
                    <h2 class="card-title" style="color:#ffd700;">{{ $totalUsers ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-voltruck">
                <div class="card-header"><i class="bi bi-person-square"></i> Admin</div>
                <div class="card-body text-center">
                    <h2 class="card-title" style="color:#ffd700;">{{ $totalAdmins ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-voltruck">
                <div class="card-header d-flex justify-content-between flex-wrap gap-2">
                    <i class="bi bi-box-seam"></i> Total Volume Scan
                    <p class="mb-0 text-white-50">m³ (dari scanner)</p>
                </div>
                <div class="card-body text-center">
                    <h2 class="card-title" style="color:#ffd700;">{{ number_format($totalVolumeScan ?? 0, 2) }}</h2>
                    
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-voltruck">
                <div class="card-header"><i class="bi bi-truck"></i>  Trucks </div>
                <div class="card-body text-center">
                    <h2 class="card-title" style="color:#ffd700;">{{ $totalTrucks ?? 0}}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2">
        <div class="col">
            <div class="card card-voltruck mt-4">
                <div class="card-header"><i class="bi bi-person-vcard"></i> Panel Admin</div>
                <div class="card-body">
                    <h6 class="mb-0">Manajemen User</h6>
                    <div class="table-responsive mt-0">
                        <table class="table table-voltruck table-bordered">
                            <thead  class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge-role">{{ ucfirst($user->role) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                   
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-voltruck mt-4">
                <div class="card-header fs-6">
                    <i class="bi bi-file-earmark-bar-graph"></i> Semua Pengukuran
                </div>
                <div class="card-body">
                    <div class="row mb-0">
                        <div class="col-md-6 mb-0">
                            <strong>Total Volume Keseluruhan:</strong> 
                            <span class="text-warning">{{ number_format($totalVolumeAll ?? 0, 2) }} m³</span>
                        </div>
                        <div class="col-md-6 mb-0">
                            <strong>Jumlah Pengukuran:</strong> 
                            <span class="text-warning">{{ $totalMeasurementsAll ?? 0}}</span>
                        </div>
                    </div>

                    @if(isset($measurements) && $measurements->count() > 0)
                        <div class="table-responsive">
                    <table class="table table-voltruck table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Driver</th>
                                <th>Waktu</th>
                                <th>Vol (m³)</th>
                                <th>Truk</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scanners as $scanner)
                            <tr>
                                <td>{{ $scanner->id }}</td>
                                <td>{{ $scanner->user->name ?? '-' }}</td>
                                <td>{{ $scanner->driver->name ?? '-' }}</td>
                                <td>{{ $scanner->scanned_at ? $scanner->scanned_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        {{ number_format($scanner->result_volume_m3, 2) }}
                                    </span>
                                </td>
                                <td>{{ $scanner->truck->plate_number ?? '-' }} - {{ $scanner->truck->name ?? '' }}</td>
                                <td>{{ $scanner->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $measurements->links('pagination::bootstrap-5') }}
    </div>
@else
    <p class="text-center py-3">Belum ada data pengukuran.</p>
@endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-2">
        <hr class="mb-0">
        <h6 class="mb-0 text-warning">📈 Laporan Volume Keseluruhan</h6>
        <p class="mt-0 text-white text-muted mt-0 bg-light">Grafik dan statistik akan ditampilkan setelah modul pengukuran selesai.</p>
        <button class="btn btn-voltruck py-0" onclick="alert('Fitur sedang dalam pengembangan')">Laporan Lengkap →</button>
    </div>
@endsection