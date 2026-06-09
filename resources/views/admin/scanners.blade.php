@extends('layouts.app')

@section('title', 'Data Scanner')

@section('content')
<div class="container-fluid mt-1">
    <div class="card card-voltruck">
        <div class="card-header bg-transparent border-bottom border-warning">
            <h5 class="mb-0 text-warning"><i class="bi bi-qr-code-scan"></i> Data Hasil Scanner</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Statistik ringkas -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="alert alert-dark bg-dark text-warning border-warning">
                        <i class="bi bi-box-seam"></i> Total Volume Scan: <strong>{{ number_format($totalVolumeScan, 2) }} m³</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-dark bg-dark text-warning border-warning">
                        <i class="bi bi-counter"></i> Jumlah Scan: <strong>{{ $totalScanCount }}</strong>
                    </div>
                </div>
            </div>

            @if($scanners->count() > 0)
                <div class="table-responsive">
                    <table class="table table-voltruck table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Truk</th>
                                <th>Driver</th>
                                <th>Tanggal Scan</th>
                                <th>Volume (m³)</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scanners as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name ?? '-' }} ({{ $item->user->role ?? 'user' }})</td>
                                <td>{{ $item->truck->plate_number ?? '-' }} - {{ $item->truck->name ?? '' }}</td>
                                <td>{{ $item->driver->name ?? '-' }}</td>
                                <td>{{ $item->scanned_at ? $item->scanned_at->format('d/m/Y H:i') : '-' }}</td>
                                <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ number_format($item->result_volume_m3, 2) }}</span></td>
                                <td>{{ $item->notes ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $scanners->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-database-slash fs-1 text-muted"></i>
                    <p class="mt-2">Belum ada data scanner.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Edit (untuk setiap scanner) -->
@foreach($scanners as $item)
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-warning">
            <div class="modal-header border-bottom border-warning">
                <h6 class="modal-title text-warning"><i class="bi bi-pencil-square"></i> Edit Data Scanner - ID: {{ $item->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.scanners.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Truk</label>
                            <select name="truck_id" class="form-select bg-dark text-white border-warning" required>
                                <option value="">-- Pilih Truk --</option>
                                @foreach($trucks as $truck)
                                    <option value="{{ $truck->id }}" {{ $item->truck_id == $truck->id ? 'selected' : '' }}>
                                        {{ $truck->plate_number }} - {{ $truck->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Driver</label>
                            <select name="driver_id" class="form-select bg-dark text-white border-warning">
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ $item->driver_id == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Volume (m³)</label>
                            <input type="number" step="0.01" name="result_volume_m3" class="form-control bg-dark text-white border-warning" value="{{ $item->result_volume_m3 }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Tanggal Scan</label>
                            <input type="datetime-local" name="scanned_at" class="form-control bg-dark text-white border-warning" value="{{ $item->scanned_at ? $item->scanned_at->format('Y-m-d\TH:i') : '' }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-warning">Catatan</label>
                        <textarea name="notes" class="form-control bg-dark text-white border-warning" rows="2">{{ $item->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-warning">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-voltruck">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus (konfirmasi) -->
<div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-danger">
            <div class="modal-header border-bottom border-danger">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data scanner untuk truk <strong>{{ $item->truck->plate_number ?? '-' }}</strong> pada tanggal <strong>{{ $item->scanned_at ? $item->scanned_at->format('d/m/Y H:i') : '-' }}</strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-top border-danger">
                <form action="{{ route('admin.scanners.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection